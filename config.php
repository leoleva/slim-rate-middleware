<?php

declare(strict_types=1);

use Nico\SlimRateMiddleware\Middleware\IpResolver;

return [
    [
        'route' => 'index',
        'limit' => 10,
        'time' => 10,
        'identifier' => IpResolver::IDENTIFIER_IP,
    ],
];
