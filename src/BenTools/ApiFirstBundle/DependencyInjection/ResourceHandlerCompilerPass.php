<?php

namespace BenTools\ApiFirstBundle\DependencyInjection;

use BenTools\ApiFirstBundle\Model\DoctrineResourceListenerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResourceHandlerCompilerPass implements CompilerPassInterface {

    const TAG = 'api_first.resource.handler';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container) {
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServices AS $id => $tags) {
            $definition = $container->findDefinition($id);
            if (in_array(DoctrineResourceListenerInterface::class, class_implements($definition->getClass()))) {
                $definition->addTag('doctrine.orm.entity_listener');
            }
        }
    }
}