<?php

namespace Wexample\SymfonyWallet\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\PhpWallet\Class\Credential;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasDateCreatedTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasTitleTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasTypeTrait;

/**
 * A credential kept in the database: its title, its type (one of
 * `wexample_symfony_wallet.types`), its parameters one row each, and how its
 * last test went. The application's subclass maps the relation to its
 * parameter entity (AbstractCredentialParameter) and creates one.
 *
 * Not a mapped superclass, like AbstractEntity above it: Doctrine maps these
 * columns straight into the concrete entity.
 */
abstract class AbstractCredential extends AbstractEntity
{
    use HasDateCreatedTrait;
    use HasTitleTrait;
    use HasTypeTrait;

    // How its last test went: asked and waiting, the connection opened, or
    // refused — the reason in the message.
    public const string CHECK_STATUS_PENDING = 'pending';

    public const string CHECK_STATUS_OK = 'ok';

    public const string CHECK_STATUS_ERROR = 'error';

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true)]
    protected ?string $checkStatus = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $checkMessage = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    protected ?DateTimeImmutable $checkedAt = null;

    /** @return Collection<int, AbstractCredentialParameter> */
    abstract public function getCredentialParameters(): Collection;

    abstract public function addCredentialParameter(AbstractCredentialParameter $parameter): static;

    abstract public function removeCredentialParameter(AbstractCredentialParameter $parameter): static;

    /** A new parameter of the app's entity, not yet added. */
    abstract protected function createCredentialParameter(): AbstractCredentialParameter;

    /** Saved and never shown again: whether it has a date of creation. */
    public function isNew(): bool
    {
        return null === $this->getDateCreated();
    }

    public function getParameterValue(string $name): ?string
    {
        foreach ($this->getCredentialParameters() as $parameter) {
            if ($parameter->getName() === $name) {
                return $parameter->getValue();
            }
        }

        return null;
    }

    /** Sets a parameter, added when missing; null removes it. */
    public function setParameterValue(string $name, ?string $value): static
    {
        foreach ($this->getCredentialParameters() as $parameter) {
            if ($parameter->getName() === $name) {
                if (null === $value) {
                    $this->removeCredentialParameter($parameter);
                } else {
                    $parameter->setValue($value);
                }

                return $this;
            }
        }

        if (null !== $value) {
            $parameter = $this->createCredentialParameter();
            $parameter->setName($name);
            $parameter->setValue($value);
            $this->addCredentialParameter($parameter);
        }

        return $this;
    }

    /** @return array<string, string> Its parameters, by name. */
    public function getParameterValues(): array
    {
        $values = [];
        foreach ($this->getCredentialParameters() as $parameter) {
            $values[(string) $parameter->getName()] = (string) $parameter->getValue();
        }

        return $values;
    }

    /** As php-wallet holds it, named by its id. */
    public function toWalletCredential(): Credential
    {
        return new Credential((string) $this->getId(), (string) $this->getType(), $this->getParameterValues());
    }

    /** Its test asked: waiting until the outcome is written. */
    public function markCheckPending(): static
    {
        $this->checkStatus = self::CHECK_STATUS_PENDING;
        $this->checkMessage = null;

        return $this;
    }

    /** Changed, its last test says nothing of it any more. */
    public function forgetCheck(): static
    {
        $this->checkStatus = null;
        $this->checkMessage = null;
        $this->checkedAt = null;

        return $this;
    }

    public function getCheckStatus(): ?string
    {
        return $this->checkStatus;
    }

    public function setCheckStatus(?string $checkStatus): static
    {
        $this->checkStatus = $checkStatus;

        return $this;
    }

    public function getCheckMessage(): ?string
    {
        return $this->checkMessage;
    }

    public function setCheckMessage(?string $checkMessage): static
    {
        $this->checkMessage = $checkMessage;

        return $this;
    }

    public function getCheckedAt(): ?DateTimeImmutable
    {
        return $this->checkedAt;
    }

    public function setCheckedAt(?DateTimeImmutable $checkedAt): static
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }
}
