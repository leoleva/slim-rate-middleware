<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Service\Resolver;

use Nico\SlimRateMiddleware\Exception\UnableToResolveIdentifierException;
use Psr\Http\Message\ServerRequestInterface;

interface ResolverInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws UnableToResolveIdentifierException
     */
    public function get(ServerRequestInterface $request): string;
}
