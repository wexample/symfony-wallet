<?php

namespace Wexample\SymfonyWallet\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Wexample\SymfonyWallet\Entity\AbstractCredential;

/**
 * A credential's test asked, its status pending: whatever opens the
 * connection — the app itself, a worker it sends the credential to — listens,
 * and writes the outcome on the credential.
 */
final class CredentialCheckAskedEvent extends Event
{
    public function __construct(public readonly AbstractCredential $credential)
    {
    }
}
