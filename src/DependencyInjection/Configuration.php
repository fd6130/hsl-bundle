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
                        ->booleanNode('enable')->defaultTrue()->end()
                        ->arrayNode('allowed_image_extension')
                            ->scalarPrototype()->end()
                        ->end()
                        ->scalarNode('save_as_extension')->defaultNull()->end()
                        ->integerNode('max_allowed_size')->defaultValue(HslImageUploadListener::MAX_ALLOWED_SIZE)->end()
                        ->integerNode('quality')->defaultValue(HslImageUploadListener::DEFAULT_QUALITY)->end()
                        ->arrayNode('resize')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enable')->defaultTrue()->end()
                                ->integerNode('max_allowed_width')->defaultValue(HslImageUploadListener::MAX_ALLOWED_WIDTH)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}