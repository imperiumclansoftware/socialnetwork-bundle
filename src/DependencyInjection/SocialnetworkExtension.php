<?php

    namespace ICS\SocialnetworkBundle\DependencyInjection;

    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Extension\Extension;
    use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
    use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

    class SocialnetworkExtension extends Extension implements PrependExtensionInterface
    {
        public function load(array $configs, ContainerBuilder $container)
        {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config/'));
            $loader->load('services.yaml');
            $configuration = new Configuration();
            $configs = $this->processConfiguration($configuration,$configs);
            $container->setParameter('socialnetwork',$configs);
        }

        public function prepend(ContainerBuilder $container)
        {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config/'));
            // Loading security config
            $loader->load('security.yaml');
            // Loading doctrine config
            $loader->load('doctrine.yaml');
            // Loading doctrine config
            $loader->load('twig.yaml');
            // Loading specific bundle config
            $bundles = $container->getParameter('kernel.bundles');
            if(isset($bundles['NavigationBundle']))
            {
               $loader->load('navigation.yaml');
            }
        }
    }
    