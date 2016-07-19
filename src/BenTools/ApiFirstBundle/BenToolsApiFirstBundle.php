<?php

namespace BenTools\ApiFirstBundle;

use BenTools\ApiFirstBundle\DependencyInjection\ResourceHandlerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BenToolsApiFirstBundle extends Bundle {

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container) {
        $container->addCompilerPass(new ResourceHandlerCompilerPass());
    }
}
