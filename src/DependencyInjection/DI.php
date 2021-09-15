<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\DependencyInjection;

use Nico\SlimRateMiddleware\Service\Handler\LimitHandler;
use Nico\SlimRateMiddleware\Service\Resolver\IpResolver;
use Nico\SlimRateMiddleware\Service\IdentifierResolverRegistry;
use Slim\Container;

class DI
{
    public function boot(Container $container): void
    {
        $this->registerResolvers($container);
        $this->registerHandler($container);
    }

    protected function registerResolvers(Container $container): void
    {
        $identifierResolverRegistry = new IdentifierResolverRegistry();
        $identifierResolverRegistry->addResolver(IpResolver::IDENTIFIER_IP, new IpResolver());

        $container[IdentifierResolverRegistry::class] = $identifierResolverRegistry;
    }

    protected function registerHandler(Container $container): void
    {
        $container[LimitHandler::class] = new LimitHandler();
    }
}
