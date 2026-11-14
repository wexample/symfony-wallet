<?php

declare(strict_types=1);

namespace Wexample\SymfonyWallet\Tests\Unit\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyWallet\Entity\AbstractCredential;
use Wexample\SymfonyWallet\Entity\AbstractCredentialParameter;

/**
 * A credential as an application maps it: its parameters under a relation
 * name of its own, which the wallet has to find.
 */
#[ORM\Entity]
#[ORM\Table(name: 'test_credential')]
class TestCredential extends AbstractCredential
{
    /** @var Collection<int, TestCredentialParameter> */
    #[ORM\OneToMany(targetEntity: TestCredentialParameter::class, mappedBy: 'owner', cascade: ['persist'], orphanRemoval: true)]
    private Collection $secrets;

    public function __construct()
    {
        parent::__construct();
        $this->secrets = new ArrayCollection();
    }

    public function getCredentialParameters(): Collection
    {
        return $this->secrets;
    }

    public function addCredentialParameter(AbstractCredentialParameter $parameter): static
    {
        if (! $this->secrets->contains($parameter)) {
            $this->secrets->add($parameter);
            $parameter->setOwner($this);
        }

        return $this;
    }

    public function removeCredentialParameter(AbstractCredentialParameter $parameter): static
    {
        $this->secrets->removeElement($parameter);

        return $this;
    }

    protected function createCredentialParameter(): AbstractCredentialParameter
    {
        return new TestCredentialParameter();
    }
}
