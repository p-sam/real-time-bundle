<?php

namespace SP\RealTimeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('sp_real_time')
            ->children()
                ->arrayNode('ably')
                    ->children()
                        ->scalarNode('api_key')
                            ->defaultValue('')
                        ->end()
                        ->integerNode('ttl')
                            ->defaultValue(3600)
                            ->min(1)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('redis')
                    ->children()
                        ->scalarNode('client')
                            ->defaultValue('')
                        ->end()
                        ->scalarNode('key_prefix')
                            ->defaultValue('')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
