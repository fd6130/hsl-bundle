<?php

namespace Fd\HslBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TransformerPass implements CompilerPassInterface
{
    const TRANSFORMER_TAG = 'fd_hsl.transformer';
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(self::TRANSFORMER_TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->setPublic(true);

            // add the transport service to the TransportChain service
            //$definition->addMethodCall('addTransport', [new Reference($id)]);
        }
    }
}