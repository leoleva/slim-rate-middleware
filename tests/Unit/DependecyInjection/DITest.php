<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Tests\DependencyInjection;

use Nico\SlimRateMiddleware\DependencyInjection\DI;
use Nico\SlimRateMiddleware\Service\Handler\LimitHandler;
use Nico\SlimRateMiddleware\Service\IdentifierResolverRegistry;
use Nico\SlimRateMiddleware\Service\Resolver\IpResolver;
use PHPUnit\Framework\TestCase;
use Slim\Container;

class DITest extends TestCase
{
    private DI $di;

    public function setUp(): void
    {
        $this->di = new DI();
    }

    public function testBoot(): void
    {
        $container = $this->createMock(Container::class);

        $container->expects(self::exactly(2))
            ->method('offsetSet')
            ->willReturnOnConsecutiveCalls(
                static function (string $class, IdentifierResolverRegistry $identifierResolverRegistry) {
                    self::assertSame(IdentifierResolverRegistry::class, $class);

                    $expectedIdentifierResolverRegistry = new IdentifierResolverRegistry();
                    $expectedIdentifierResolverRegistry->addResolver(IpResolver::IDENTIFIER_IP, new IpResolver());

                    self::assertEquals($expectedIdentifierResolverRegistry, $identifierResolverRegistry);
                },
                static function (string $class, LimitHandler $limitHandler) {
                    self::assertSame(LimitHandler::class, $class);
                    self::assertEquals(new LimitHandler(), $limitHandler);
                },
            )
        ;

        $this->di->boot($container);
    }
}
