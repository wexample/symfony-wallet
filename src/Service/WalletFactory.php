<?php

namespace Wexample\SymfonyWallet\Service;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Wexample\PhpWallet\Class\Wallet;
use Wexample\SymfonyWallet\Entity\AbstractCredential;
use Wexample\SymfonyWallet\Entity\AbstractCredentialParameter;

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
        foreach ($this->findCredentials($this->credentialClass) as $credential) {
            $wallet->add($credential->toWalletCredential());
        }

        return $wallet;
    }

    /**
     * Every credential with its parameters, in one query: the relation is
     * mapped by the application's subclass, under a name of its own, found
     * here as the one leading to parameters. None found, the rows are read as
     * they are, their parameters each in turn.
     *
     * @param class-string<AbstractCredential> $credentialClass
     * @return iterable<AbstractCredential>
     */
    private function findCredentials(string $credentialClass): iterable
    {
        $metadata = $this->entityManager->getClassMetadata($credentialClass);

        foreach ($metadata->getAssociationNames() as $association) {
            if ($metadata->isCollectionValuedAssociation($association)
                && is_a($metadata->getAssociationTargetClass($association), AbstractCredentialParameter::class, true)) {
                return $this->entityManager->createQueryBuilder()
                    ->select('credential', 'parameter')
                    ->from($credentialClass, 'credential')
                    ->leftJoin('credential.'.$association, 'parameter')
                    ->getQuery()
                    ->getResult();
            }
        }

        return $this->entityManager->getRepository($credentialClass)->findAll();
    }
}
