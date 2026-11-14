<?php

declare(strict_types=1);

namespace Wexample\SymfonyWallet\Tests\Unit\Fixtures;

use Doctrine\ORM\Mapping as ORM;
use Wexample\SymfonyWallet\Entity\AbstractCredential;
use Wexample\SymfonyWallet\Entity\AbstractCredentialParameter;

#[ORM\Entity]
#[ORM\Table(name: 'test_credential_parameter')]
class TestCredentialParameter extends AbstractCredentialParameter
{
    #[ORM\ManyToOne(targetEntity: TestCredential::class, inversedBy: 'secrets')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TestCredential $owner = null;

    public function getCredential(): ?AbstractCredential
    {
        return $this->owner;
    }

    public function setOwner(TestCredential $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
