<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Client;

/**
 * @method mixed set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
 * @method string|null get($key)
 * @method int ttl($key)
 */
interface RedisClientInterface
{
}
