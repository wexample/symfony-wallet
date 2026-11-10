<?php

namespace Wexample\SymfonyWallet\Interface;

use Wexample\SymfonyWallet\Entity\AbstractCredential;

/**
 * What in the app names credentials — a storage, a model provider, a node's
 * configuration —, counted; one named somewhere is not removed. Every
 * implementation is summed (CredentialUsageService).
 */
interface CredentialUsageCounterInterface
{
    /**
     * @param list<AbstractCredential> $credentials
     *
     * @return array<string, int> How many things name each, by its id; one left out counts none.
     */
    public function countUsages(array $credentials): array;
}
