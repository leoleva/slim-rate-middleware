<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Middleware;

use Nico\SlimRateMiddleware\Client\RedisClientInterface;
use Nico\SlimRateMiddleware\DTO\Config;
use Nico\SlimRateMiddleware\Exception\CouldNotResolveRouteException;
use Nico\SlimRateMiddleware\Exception\ResolverNotFoundException;
use Nico\SlimRateMiddleware\Exception\UnableToResolveIdentifierException;
use Nico\SlimRateMiddleware\Service\IdentifierResolverRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Route;

class AppRateLimitMiddleware
{
    private const KEY_PREFIX = 'rate_middleware:%s_%s';

    private RedisClientInterface $redisClient;
    private IdentifierResolverRegistry $identifierResolverRegistry;

    /**
     * @var callable
     */
    private $handler;

    /**
     * @var Config[]
     */
    private array $configs;

    public function __construct(
        RedisClientInterface $redisClient,
        IdentifierResolverRegistry $identifierResolverRegistry,
        array $configs,
        callable $handler
    ) {
        $this->redisClient = $redisClient;
        $this->identifierResolverRegistry = $identifierResolverRegistry;
        $this->handler = $handler;
        $this->configs = $configs;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return false|mixed
     * @throws CouldNotResolveRouteException
     * @throws ResolverNotFoundException
     * @throws UnableToResolveIdentifierException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        foreach ($this->configs as $configMap) {
            $routeName = $this->getRouteName($request);
            $config = Config::fromArray($configMap);

            if ($config->getRouteName() !== $routeName) {
                continue;
            }

            $cacheKey = $this->getCacheKey($routeName, $this->getRequesterIdentifier($config, $request));
            $count = $this->getCount($cacheKey);

            ++$count;

            if ($count >= $config->getLimit()) {
                return call_user_func($this->handler, $request, $response);
            }

            $this->saveCount($config, $cacheKey, $count);
        }

        return $next($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws CouldNotResolveRouteException
     */
    private function getRouteName(ServerRequestInterface $request): string
    {
        $route = $request->getAttribute('route');

        if ($route instanceof Route) {
            return $route->getName();
        }

        throw new CouldNotResolveRouteException();
    }

    private function getCount(string $cacheKey): int
    {
        return (int)$this->redisClient->get($cacheKey);
    }

    private function getCacheKey(string $routeName, string $requestIdentifier): string
    {
        return sprintf(self::KEY_PREFIX, $routeName, $requestIdentifier);
    }

    /**
     * @param Config $config
     * @param ServerRequestInterface $request
     * @return string
     * @throws ResolverNotFoundException
     * @throws UnableToResolveIdentifierException
     */
    private function getRequesterIdentifier(Config $config, ServerRequestInterface $request): string
    {
        return $this->identifierResolverRegistry->getResolver($config->getIdentifier())->get($request);
    }

    private function saveCount(Config $config, string $cacheKey, int $count): void
    {
        $ttl = $this->redisClient->ttl($cacheKey);

        if (in_array($ttl, [0, -1, -2], true)) {
            $ttl = $config->getTime();
        }

        $this->redisClient->set($cacheKey, $count, $ttl);
    }
}
