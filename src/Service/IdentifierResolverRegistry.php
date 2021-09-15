<?php

declare(strict_types=1);

namespace Nico\SlimRateMiddleware\Service;

use Nico\SlimRateMiddleware\Service\Resolver\ResolverInterface;
use Nico\SlimRateMiddleware\Exception\ResolverNotFoundException;

class IdentifierResolverRegistry
{
    /**
     * @var ResolverInterface[]
     */
    private array $resolvers;

    public function addResolver(string $identifier, ResolverInterface $resolver): self
    {
        $this->resolvers[$identifier] = $resolver;

        return $this;
    }

    /**
     * @param string $identifier
     * @return ResolverInterface
     * @throws ResolverNotFoundException
     */
    public function getResolver(string $identifier): ResolverInterface
    {
        $resolver = $this->resolvers[$identifier] ?? null;

        if ($resolver === null) {
            throw new ResolverNotFoundException();
        }

        return $resolver;
    }
}
