<?php

namespace Wexample\SymfonyWallet\Service;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Wexample\PhpWallet\Class\Wallet;
use Wexample\SymfonyWallet\Entity\AbstractCredential;

/**
 * The wallet of the credentials the app keeps in its database: each row of
 * its credential entity, checked against its type. Built when asked, so an
 * edit is read at once.
 */
final readonly class WalletFactory
{
    /**
     * @param class-string<AbstractCredential>|null $credentialClass
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CredentialTypeRegistry $typeRegistry,
        private ?string $credentialClass,
    ) {
    }

    public function create(): Wallet
    {
        if (null === $this->credentialClass) {
            throw new LogicException('No credential entity: set wexample_symfony_wallet.credential_class.');
        }

        $wallet = new Wallet(array_values($this->typeRegistry->all()));

        /** @var AbstractCredential $credential */
        foreach ($this->entityManager->getRepository($this->credentialClass)->findAll() as $credential) {
            $wallet->add($credential->toWalletCredential());
        }

        return $wallet;
    }
}
