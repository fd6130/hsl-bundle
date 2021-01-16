<?php

namespace Fd\HslBundle\DependencyInjection;

use Fd\HslBundle\Event\Listener\HslImageUploadListener;
use Fd\HslBundle\Pagination\PaginatorInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('fd_hsl');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('paginator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('default_limit')->defaultValue(PaginatorInterface::DEFAULT_LIMIT_VALUE)->end()
                    ->end()
                ->end()
                ->arrayNode('hsl_image_upload_listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enable')->defaultFalse()->end()
                        ->scalarNode('save_as_extension')->defaultNull()->end()
                        ->integerNode('quality')->defaultValue(HslImageUploadListener::DEFAULT_QUALITY)->end()
                        ->arrayNode('resize')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enable')->defaultFalse()->end()
                                ->integerNode('resize_when_width_exceed')->defaultValue(HslImageUploadListener::DEFAULT_RESIZE_WHEN_WIDTH_EXCEED)->end()
                                ->integerNode('resize_to_width')->defaultValue(HslImageUploadListener::DEFAULT_RESIZE_TO_WIDTH)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}