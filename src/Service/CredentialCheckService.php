<?php

namespace Wexample\SymfonyWallet\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Wexample\SymfonyWallet\Entity\AbstractCredential;
use Wexample\SymfonyWallet\Event\CredentialCheckAskedEvent;

/**
 * A credential's test: asked — pending, saved, then said to whatever opens
 * the connection (CredentialCheckAskedEvent) —, and its outcome read.
 */
final readonly class CredentialCheckService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function ask(AbstractCredential $credential): void
    {
        $credential->markCheckPending();
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new CredentialCheckAskedEvent($credential));
    }

    /** @return array{status: ?string, message: ?string, checkedAt: ?string} */
    public function getOutcome(AbstractCredential $credential): array
    {
        return [
            'status' => $credential->getCheckStatus(),
            'message' => $credential->getCheckMessage(),
            'checkedAt' => $credential->getCheckedAt()?->format(DATE_ATOM),
        ];
    }
}
