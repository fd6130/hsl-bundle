<?php

namespace Fd\HslBundle;

use Fd\HslBundle\DependencyInjection\Compiler\HslInterfacePass;
use Fd\HslBundle\DependencyInjection\Compiler\TransformerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FdHslBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransformerPass());
    }
}