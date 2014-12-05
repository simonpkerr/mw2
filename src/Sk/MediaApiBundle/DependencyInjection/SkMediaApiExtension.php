<?php

namespace Sk\MediaApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SkMediaApiExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        //mediaapi params
        $container->setParameter('media_provider_api.debug_mode', $config['media_provider_api']['debug_mode']);
        $container->setParameter('media_provider_api.providers', $config['media_provider_api']['providers']);

        $providers = $config['media_provider_api']['providers'];
        //amazon params
        $container->setParameter('amazon_provider.access_params', $providers['amazon_provider']['access_params']);
        $container->setParameter ('amazon_provider.amazon_signed_request.class', $providers['amazon_provider']['amazon_signed_request']['class']);
        
        //youtube params
        $container->setParameter('youtube_provider.youtube_request_object.class', $providers['youtube_provider']['youtube_request_object']['class']);
    }
}
