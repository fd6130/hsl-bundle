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
            ->end()
        ;

        return $treeBuilder;
    }
}