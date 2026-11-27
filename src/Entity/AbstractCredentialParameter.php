<?php

namespace Wexample\SymfonyWallet\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;
use Wexample\SymfonyWallet\Doctrine\SecretTextType;

/**
 * One parameter of a credential, by name; its value kept encrypted at rest
 * and read in clear (SecretTextType) — a host as a password: what a
 * credential holds is never left readable in the base. The application's
 * subclass maps the relation to its credential entity.
 *
 * Not a mapped superclass, like AbstractEntity above it: Doctrine maps these
 * columns straight into the concrete entity.
 */
abstract class AbstractCredentialParameter extends AbstractEntity
{
    use HasNameTrait;

    #[ORM\Column(type: SecretTextType::NAME, nullable: true)]
    protected ?string $value = null;

    abstract public function getCredential(): ?AbstractCredential;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }
}
