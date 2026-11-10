<?php

namespace Wexample\SymfonyWallet\Entity;

use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasValueTrait;

/**
 * One parameter of a credential, by name. The application's subclass maps
 * the relation to its credential entity.
 *
 * Not a mapped superclass, like AbstractEntity above it: Doctrine maps these
 * columns straight into the concrete entity.
 */
abstract class AbstractCredentialParameter extends AbstractEntity
{
    use HasNameTrait;
    use HasValueTrait;

    abstract public function getCredential(): ?AbstractCredential;
}
