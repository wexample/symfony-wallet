<?php

namespace Wexample\SymfonyWallet\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;
use Wexample\SymfonyWallet\Service\CredentialTypeRegistry;
use Wexample\SymfonyWallet\Service\WalletFactory;

class WexampleSymfonyWalletExtension extends AbstractWexampleSymfonyExtension
{
    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->loadConfig(
            __DIR__,
            $container
        );

        $container->getDefinition(CredentialTypeRegistry::class)
            ->setArgument('$types', $config['types']);
        $container->getDefinition(WalletFactory::class)
            ->setArgument('$credentialClass', $config['credential_class']);
    }
}
