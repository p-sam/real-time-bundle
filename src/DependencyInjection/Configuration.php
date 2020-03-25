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
        $treeBuilder = new TreeBuilder('sp_real_time');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
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
                        ->integerNode('force_client_ttl')
                            ->defaultValue(null)
                            ->validate()
                            ->ifTrue(static function ($v) {
                                if(null === $v) {
                                    return false;
                                }
                                if (!is_numeric($v)) {
                                    return true;
                                }

                                if (is_int($v)) {
                                    return false;
                                }

                                if ($v > 1) {
                                    return false;
                                }

                                return true;
                            })
                            ->thenInvalid('Expected an int > 1 or null ; had %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('redis')
                    ->children()
                        ->scalarNode('key_prefix')
                            ->defaultValue('')
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('presence_check')
                    ->defaultTrue()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
