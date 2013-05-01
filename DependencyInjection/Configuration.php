<?php

namespace Axsy\TransactionalBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Doctrine\DBAL\Connection;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('axsy_transactional');

        $rootNode
            ->children()
                ->scalarNode('default_connection')
                    ->cannotBeEmpty()
                    ->defaultValue('default')
                ->end()
                ->scalarNode('default_isolation')
                    ->cannotBeEmpty()
                    ->defaultValue(Connection::TRANSACTION_READ_COMMITTED)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
