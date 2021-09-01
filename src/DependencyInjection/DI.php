<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\DI;

use Nico\SlimRateMiddleware\Service\Handler\LimitHandler;
use Nico\SlimRateMiddleware\Service\Resolver\IpResolver;
use Nico\SlimRateMiddleware\Service\ResolverRegister;
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
        $resolverRegistry = new ResolverRegister();
        $resolverRegistry->addResolver(IpResolver::IDENTIFIER_IP, new IpResolver());

        $container[ResolverRegister::class] = $resolverRegistry;
    }

    protected function registerHandler(Container $container): void
    {
        $container[LimitHandler::class] = new LimitHandler();
    }
}
