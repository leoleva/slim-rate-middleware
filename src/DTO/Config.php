<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\DTO;

use InvalidArgumentException;

class Config
{
    private string $routeName;
    private int $limit;
    private int $time;
    private string $identifier;

    private function __construct(string $routeName, int $limit, int $time, string $identifier)
    {
        $this->routeName = $routeName;
        $this->limit = $limit;
        $this->time = $time;
        $this->identifier = $identifier;
    }

    public static function fromArray(array $config): self
    {
        if (!isset($config['route']) || !is_string($config['route'])) {
            throw new InvalidArgumentException('Route parameter is missing or isn\'t string');
        }

        if (!isset($config['limit']) || !is_int($config['limit'])) {
            throw new InvalidArgumentException('Limit parameter is missing or isin\'t integer');
        }

        if (!isset($config['time']) || !is_int($config['time'])) {
            throw new InvalidArgumentException('Time parameter is missing or isin\'t integer');
        }

        if (!isset($config['identifier']) || !is_string($config['identifier'])) {
            throw new InvalidArgumentException('Identifier parameter is missing or isin\'t integer');
        }

        return new self(
            $config['route'],
            $config['limit'],
            $config['time'],
            $config['identifier'],
        );
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
