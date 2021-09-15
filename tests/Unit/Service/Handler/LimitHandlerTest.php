<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Tests\Service\Handler;

use Nico\SlimRateMiddleware\Service\Handler\LimitHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LimitHandlerTest extends TestCase
{
    private LimitHandler $limitHandler;

    public function setUp(): void
    {
        $this->limitHandler = new LimitHandler();
    }

    public function testInvoke(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->expects(self::once())
            ->method('withStatus')
            ->with(429)
            ->willReturnSelf()
        ;

        self::assertSame(
            $response,
            $this->limitHandler->__invoke($request, $response)
        );
    }
}
