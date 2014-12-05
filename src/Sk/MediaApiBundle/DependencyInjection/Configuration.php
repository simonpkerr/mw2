<?php

namespace Sk\MediaApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $rootNode = $treeBuilder->root('sk_media_api');
        $rootNode
        ->children()
            ->arrayNode('media_provider_api')->isRequired()
                ->children()
                    ->booleanNode('debug_mode')->defaultValue(false)->end()
                    ->arrayNode('providers')->isRequired()
                        ->children()
                            ->arrayNode('amazon_provider')
                            ->isRequired()
                                ->children()
                                    ->arrayNode('access_params')->isRequired()
                                        ->children()
                                            ->scalarNode('amazon_public_key')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('amazon_private_key')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('amazon_associate_tag')->isRequired()->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()//end of access params
                                    ->arrayNode('amazon_signed_request')
                                    ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('class')->defaultValue('Sk\MediaApiBundle\MediaProviderApi\AmazonSignedRequest')->end()
                                        ->end()
                                    ->end()//end of amazon_signed_request
                                ->end()
                            ->end()//end of amazonapi
                            ->arrayNode('youtube_provider')
                            ->isRequired()
                                ->children()
                                    ->arrayNode('youtube_request_object')
                                    ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('class')->defaultValue('Zend_Gdata_YouTube')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()//end of youtubeapi
                        ->end()
                    ->end()//end of providers
                ->end()

            ->end()//end of media_provider_api                
        ->end()
        ;
        
        return $treeBuilder;
    }
}
