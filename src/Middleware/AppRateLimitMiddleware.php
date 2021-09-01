<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Middleware;

use Nico\SlimRateMiddleware\Client\RedisClientInterface;
use Nico\SlimRateMiddleware\DTO\Config;
use Nico\SlimRateMiddleware\Exception\CouldNotResolveRouteException;
use Nico\SlimRateMiddleware\Exception\InvalidArgumentException;
use Nico\SlimRateMiddleware\Exception\RateMiddlewareException;
use Nico\SlimRateMiddleware\Service\ResolverRegister;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Route;

class AppRateLimitMiddleware
{
    private const KEY_PREFIX = 'rate_middleware:%s_%s';

    private RedisClientInterface $redisClient;
    private ResolverRegister $resolverRegister;

    /**
     * @var callable
     */
    private $handler;

    /**
     * @var mixed[]
     */
    private array $configs;

    public function __construct(
        RedisClientInterface $redisClient,
        ResolverRegister $resolverRegister,
        array $configs,
        callable $handler
    ) {
        $this->redisClient = $redisClient;
        $this->resolverRegister = $resolverRegister;
        $this->handler = $handler;
        $this->configs = $configs;

        $this->validateConfigs();
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     * @throws RateMiddlewareException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        foreach ($this->configs as $configMap) {
            $routeName = $this->getRouteName($request);
            $config = Config::fromArray($configMap);

            if ($config->getRouteName() !== $routeName) {
                continue;
            }

            $identifier = $this->getIdentifier($config, $request);
            $count = $this->getCount($identifier);

            ++$count;

            if ($count >= $config->getLimit()) {
                return call_user_func($this->handler, $request, $response);
            }

            $this->saveCount($config, $identifier, $count);
        }

        return $next($request, $response);
    }

    private function getCount(string $identifier): int
    {
        return (int)$this->redisClient->get($this->getKey($identifier));
    }

    private function getKey(string $routeName, string $identifierKey): string
    {
        return sprintf(self::KEY_PREFIX, $routeName, $identifierKey);
    }

    private function getIdentifier(Config $config, ServerRequestInterface $request): string
    {
        return $this->resolverRegister->getResolver($config->getIdentifier())->get($request);
    }

    private function saveCount(Config $config, string $identifier, int $count): void
    {
        $ttl = $this->redisClient->ttl($identifier);

        if (in_array($ttl, [0, -1, -2])) {
            $ttl = $config->getTime();
        }

        $this->redisClient->set($this->getKey($identifier), $count, $ttl);
    }

    private function validateConfigs(): void
    {
        foreach ($this->configs as $config) {
            if (!is_array($config)) {
                throw new InvalidArgumentException('Config must be multidimensional');
            }
        }
    }

    private function getRouteName(ServerRequestInterface $request): string
    {
        $route = $request->getAttribute('route');

        if ($route instanceof Route) {
            return $route->getName();
        }

        throw new CouldNotResolveRouteException();
    }
}
