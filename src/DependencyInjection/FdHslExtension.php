<?php

namespace Fd\HslBundle\DependencyInjection;

use Fd\HslBundle\Event\Listener\HslImageUploadListener;
use Fd\HslBundle\HslInterface;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class FdHslExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(TransformerAbstract::class)
            ->addTag('fd_hsl.transformer')
        ;

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $paginatorDefinition = $container->getDefinition('fdhsl.pagination.paginator');
        $paginatorDefinition->setArgument(2, $config['paginator']['default_limit']);

        $hslImageUpload = $container->getDefinition(HslImageUploadListener::class);
        $hslImageUpload->setArgument(0, $config['hsl_image_upload_listener']);
    }
}