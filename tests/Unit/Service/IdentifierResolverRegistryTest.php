<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Tests\Service;

use Nico\SlimRateMiddleware\Exception\ResolverNotFoundException;
use Nico\SlimRateMiddleware\Service\IdentifierResolverRegistry;
use Nico\SlimRateMiddleware\Service\Resolver\ResolverInterface;
use PHPUnit\Framework\TestCase;

class IdentifierResolverRegistryTest extends TestCase
{
    private IdentifierResolverRegistry $identifierResolverRegistry;

    public function setUp(): void
    {
        $this->identifierResolverRegistry = new IdentifierResolverRegistry();
    }

    public function testGetExistingResolver(): void
    {
        $identifier = 'test_identifier';
        $resolver = $this->createMock(ResolverInterface::class);

        $this->identifierResolverRegistry->addResolver($identifier, $resolver);

        self::assertSame(
            $resolver,
            $this->identifierResolverRegistry->getResolver($identifier)
        );
    }

    public function testGetResolverWhenResolverDoesntExist(): void
    {
        $this->expectException(ResolverNotFoundException::class);

        $this->identifierResolverRegistry->getResolver('random_identifier');
    }
}
