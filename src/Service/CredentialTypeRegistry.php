<?php

namespace Wexample\SymfonyWallet\Service;

use Wexample\PhpWallet\Class\CredentialType;
use Wexample\SymfonyHelpers\Attribute\WorkerSafe;

/**
 * The types of credential the app declares (`wexample_symfony_wallet.types`),
 * as php-wallet's CredentialType, with the icon a screen draws each with.
 */
#[WorkerSafe('Built once from the bundle configuration.')]
final class CredentialTypeRegistry
{
    /** @var array<string, CredentialType> */
    private array $types = [];

    /** @var array<string, string> */
    private array $icons = [];

    /**
     * @param array<string, array{label: ?string, icon: ?string, parameters: list<array>}> $types
     */
    public function __construct(array $types)
    {
        foreach ($types as $name => $type) {
            $this->types[$name] = CredentialType::fromArray([
                'name' => $name,
                'label' => $type['label'],
                'parameters' => array_map(
                    static fn (array $parameter): array => array_filter($parameter, static fn (mixed $value): bool => null !== $value),
                    $type['parameters'],
                ),
            ]);
            if ($type['icon']) {
                $this->icons[$name] = $type['icon'];
            }
        }
    }

    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }

    public function get(string $name): ?CredentialType
    {
        return $this->types[$name] ?? null;
    }

    /** @return array<string, CredentialType> */
    public function all(): array
    {
        return $this->types;
    }

    /** @return list<string> */
    public function names(): array
    {
        return array_keys($this->types);
    }

    public function getLabel(string $name): string
    {
        return ($this->types[$name] ?? null)?->getLabel() ?? $name;
    }

    public function getIcon(string $name): ?string
    {
        return $this->icons[$name] ?? null;
    }

    public function isSecret(string $type, string $parameter): bool
    {
        return true === $this->get($type)?->isSecret($parameter);
    }
}
