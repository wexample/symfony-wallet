<?php

namespace Wexample\SymfonyWallet\Service;

use Wexample\SymfonyWallet\Entity\AbstractCredential;

/**
 * A credential as the screens read it, in the api's shape of an entity
 * (`{type, entity, metadata, relationships}`): its parameters — a secret's
 * value never, whether one is set alone —, where it connects, its last test;
 * in a list, what names it and what may be done to it besides.
 */
final readonly class CredentialPresenter
{
    public const string ENTITY_TYPE = 'credential';

    public function __construct(
        private CredentialTypeRegistry $typeRegistry,
    ) {
    }

    /**
     * @param array{usages?: array<string, int>, actions?: array<string, list<array>>} $context
     */
    public function present(AbstractCredential $credential, array $context = []): array
    {
        $type = (string) $credential->getType();
        $id = (string) $credential->getId();
        $parameters = [];

        foreach ($credential->getParameterValues() as $name => $value) {
            $secret = $this->typeRegistry->isSecret($type, $name);
            $parameters[] = ['name' => $name, 'value' => $secret ? null : $value, 'secret' => $secret];
        }

        return [
            'type' => self::ENTITY_TYPE,
            'entity' => [
                'id' => $id,
                'title' => $credential->getTitle(),
                'type' => $type,
                'typeTitle' => $this->typeRegistry->getLabel($type),
                'typeIcon' => $this->typeRegistry->getIcon($type),
                'dateCreated' => $credential->getDateCreated()?->format(DATE_ATOM),
                'credentialParameters' => $parameters,
                // Where it connects, to tell two apart: its host, else its path.
                'target' => $credential->getParameterValue('host') ?? $credential->getParameterValue('path'),
                'checkStatus' => $credential->getCheckStatus(),
                'checkMessage' => $credential->getCheckMessage(),
                'checkedAt' => $credential->getCheckedAt()?->format(DATE_ATOM),
                'usage' => $context['usages'][$id] ?? null,
                'actions' => $context['actions'][$id] ?? [],
            ],
            'metadata' => [],
            'relationships' => [],
        ];
    }

    /**
     * @param list<AbstractCredential> $credentials
     */
    public function presentCollection(array $credentials, array $context = []): array
    {
        return array_map(fn (AbstractCredential $credential): array => $this->present($credential, $context), $credentials);
    }
}
