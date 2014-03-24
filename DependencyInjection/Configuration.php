<?php

namespace PHPResqueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * PHPResqueBundle Configuration class.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('phpresque');

        $rootNode
            ->children()
                ->scalarNode('backend')->defaultValue('127.0.0.1:6379')->end()
            ->end();

        return $treeBuilder;
    }
}