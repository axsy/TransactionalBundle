<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axsy\TransactionalBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Doctrine\DBAL\Connection;

/**
 * This is the class that validates and merges configuration from app/config files
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
                    ->defaultValue('read_committed')
                    ->info('Supported isolations are read_uncommitted, read_committed, repeatable_read, serializable')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
