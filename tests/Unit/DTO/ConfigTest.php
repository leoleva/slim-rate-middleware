<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Tests\DTO;

use InvalidArgumentException;
use Nico\SlimRateMiddleware\DTO\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private const TEST_ROUTE = 'app_ping_v1';
    private const TEST_LIMIT = 202;
    private const TEST_TIME = 404;
    private const TEST_IDENTIFIER = 'ip';

    public function testFromArray(): void
    {
        $config = Config::fromArray($this->getValidConfig());

        self::assertSame(self::TEST_ROUTE, $config->getRouteName());
        self::assertSame(self::TEST_LIMIT, $config->getLimit());
        self::assertSame(self::TEST_TIME, $config->getTime());
        self::assertSame(self::TEST_IDENTIFIER, $config->getIdentifier());
    }

    /**
     * @param array $config
     * @param string $message
     * @dataProvider dataProviderWithInvalidArrays
     */
    public function testWithInvalidArray(array $config, string $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        Config::fromArray($config);
    }

    public function dataProviderWithInvalidArrays(): array
    {
        return [
            [
                [],
                'route_name parameter is missing or isn\'t string',
            ],
            [
                [
                    'route_name' => [],
                ],
                'route_name parameter is missing or isn\'t string',
            ],
            [
                [
                    'route_name' => self::TEST_ROUTE,
                ],
                'limit parameter is missing or isin\'t integer',
            ],
            [
                [
                    'route_name' => self::TEST_ROUTE,
                    'limit' => [],
                ],
                'limit parameter is missing or isin\'t integer',
            ],
            [
                [
                    'route_name' => self::TEST_ROUTE,
                    'limit' => self::TEST_LIMIT,
                ],
                'time parameter is missing or isin\'t integer',
            ],
            [
                [
                    'route_name' => self::TEST_ROUTE,
                    'limit' => self::TEST_LIMIT,
                    'time' => [],
                ],
                'time parameter is missing or isin\'t integer',
            ],
            [
                [
                    'route_name' => self::TEST_ROUTE,
                    'limit' => self::TEST_LIMIT,
                    'time' => self::TEST_TIME,
                ],
                'identifier parameter is missing or isin\'t integer',
            ],
            [
                [
                    'route_name' => self::TEST_ROUTE,
                    'limit' => self::TEST_LIMIT,
                    'time' => self::TEST_TIME,
                    'identifier' => [],
                ],
                'identifier parameter is missing or isin\'t integer',
            ],
        ];
    }

    private function getValidConfig(): array
    {
        return [
            'route_name' => self::TEST_ROUTE,
            'limit' => self::TEST_LIMIT,
            'time' => self::TEST_TIME,
            'identifier' => self::TEST_IDENTIFIER,
        ];
    }
}
