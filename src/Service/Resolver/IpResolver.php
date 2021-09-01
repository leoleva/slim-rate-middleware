<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Service\Resolver;

use Nico\SlimRateMiddleware\Exception\UnableToResolveIdentifierException;
use Psr\Http\Message\ServerRequestInterface;

class IpResolver implements ResolverInterface
{
    public const IDENTIFIER_IP = 'ip';

    public function get(ServerRequestInterface $request): string
    {
        $attributeIp = $request->getAttribute('ip_address');

        if ($attributeIp !== null) {
            return $attributeIp;
        }

        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? null;

        if ($ip === null) {
            throw new UnableToResolveIdentifierException();
        }

        return $ip;
    }
}
