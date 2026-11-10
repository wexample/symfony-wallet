<?php

namespace Wexample\SymfonyWallet\Service;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Wexample\SymfonyWallet\Entity\AbstractCredential;
use Wexample\SymfonyWallet\Interface\CredentialUsageCounterInterface;

/**
 * How many things name each credential, every counter of the app summed.
 */
final readonly class CredentialUsageService
{
    /**
     * @param iterable<CredentialUsageCounterInterface> $counters
     */
    public function __construct(
        #[AutowireIterator(CredentialUsageCounterInterface::class)]
        private iterable $counters,
    ) {
    }

    /**
     * @param list<AbstractCredential> $credentials
     *
     * @return array<string, int>
     */
    public function countUsages(array $credentials): array
    {
        $counts = array_fill_keys(array_map(static fn (AbstractCredential $credential): string => (string) $credential->getId(), $credentials), 0);

        if (! $counts) {
            return $counts;
        }

        foreach ($this->counters as $counter) {
            foreach ($counter->countUsages($credentials) as $id => $count) {
                $counts[$id] = ($counts[$id] ?? 0) + $count;
            }
        }

        return $counts;
    }

    public function isUsed(AbstractCredential $credential): bool
    {
        return 0 < $this->countUsages([$credential])[(string) $credential->getId()];
    }
}
