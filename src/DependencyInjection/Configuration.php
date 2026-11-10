<?php

namespace Wexample\SymfonyWallet\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * The credential entity the app maps, and the types its credentials take —
 * the shape php-wallet's CredentialType reads, an icon besides for the
 * screens:
 *
 *     wexample_symfony_wallet:
 *         credential_class: App\Entity\Credential
 *         types:
 *             postgres:
 *                 label: PostgreSQL
 *                 icon: ph:bold/database
 *                 parameters:
 *                     - { name: host }
 *                     - { name: port, default: '5432' }
 *                     - { name: password, secret: true, required: false }
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('wexample_symfony_wallet');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('credential_class')
                    ->defaultNull()
                    ->info('The app\'s entity extending AbstractCredential; the wallet is built from its rows.')
                ->end()
                ->arrayNode('types')
                    ->info('The types of credential, by name: `postgres`, `open_ai`, `ssh`…')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->defaultNull()->end()
                            ->scalarNode('icon')->defaultNull()->end()
                            ->arrayNode('parameters')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('label')->defaultNull()->end()
                                        ->booleanNode('required')->defaultTrue()->end()
                                        ->booleanNode('secret')->defaultFalse()->end()
                                        ->scalarNode('default')->defaultNull()->end()
                                        ->arrayNode('choices')->scalarPrototype()->end()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
