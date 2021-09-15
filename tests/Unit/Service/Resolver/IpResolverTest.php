<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Tests\Service\Resolver;

use Nico\SlimRateMiddleware\Exception\UnableToResolveIdentifierException;
use Nico\SlimRateMiddleware\Service\Resolver\IpResolver;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class IpResolverTest extends TestCase
{
    private const TEST_IP = '127.0.0.1';

    private IpResolver $ipResolver;

    public function setUp(): void
    {
        $this->ipResolver = new IpResolver();
    }

    public function testWhenIPAddressAttributeIsSet(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getAttribute')
            ->with('ip_address')
            ->willReturn(self::TEST_IP)
        ;

        self::assertSame(self::TEST_IP, $this->ipResolver->get($request));
    }

    public function testWhenRemoteAddressExistsInServerParams(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getAttribute')
            ->with('ip_address')
            ->willReturn(null)
        ;

        $request->expects(self::once())
            ->method('getServerParams')
            ->willReturn(['REMOTE_ADDR' => self::TEST_IP])
        ;

        self::assertSame(
            self::TEST_IP,
            $this->ipResolver->get($request)
        );
    }

    public function testFailure(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('getAttribute')
            ->with('ip_address')
            ->willReturn(null)
        ;

        $request->expects(self::once())
            ->method('getServerParams')
            ->willReturn([])
        ;

        $this->expectException(UnableToResolveIdentifierException::class);

        $this->ipResolver->get($request);
    }
}
