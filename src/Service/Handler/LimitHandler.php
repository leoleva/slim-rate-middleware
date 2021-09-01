<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Service\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LimitHandler
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response->withStatus(429);
    }
}
