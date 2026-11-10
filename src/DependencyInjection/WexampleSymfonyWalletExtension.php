<?php

namespace Wexample\SymfonyWallet\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;
use Wexample\SymfonyWallet\Interface\CredentialUsageCounterInterface;
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

        // The app's credential entity, for whoever reads its rows.
        $container->setParameter('wexample_symfony_wallet.credential_class', $config['credential_class']);

        $container->getDefinition(CredentialTypeRegistry::class)
            ->setArgument('$types', $config['types']);
        $container->getDefinition(WalletFactory::class)
            ->setArgument('$credentialClass', $config['credential_class']);
        $container->registerForAutoconfiguration(CredentialUsageCounterInterface::class)
            ->addTag(CredentialUsageCounterInterface::class);
    }
}
