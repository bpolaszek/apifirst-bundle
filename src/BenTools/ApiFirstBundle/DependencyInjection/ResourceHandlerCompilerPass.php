<?php

namespace BenTools\ApiFirstBundle\DependencyInjection;

use BenTools\ApiFirstBundle\Model\DoctrineResourceListenerInterface;
use BenTools\ApiFirstBundle\Services\ResourceHandlerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ResourceHandlerCompilerPass implements CompilerPassInterface {

    const TAG = 'api_first.resource.handler';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container) {

        $resourceHandlerRegistryDefinition = $container->findDefinition('api_first.resource_handler_registry');
        $taggedServices                    = $container->findTaggedServiceIds(self::TAG);

        foreach ($taggedServices AS $id => $tags) {

            $definition = $container->findDefinition($id);

            $resourceHandlerRegistryDefinition->addMethodCall('registerResourceHandler', [new Reference($id)]);

            if (in_array(DoctrineResourceListenerInterface::class, class_implements($definition->getClass()))) {
                $definition->addTag('doctrine.orm.entity_listener');
            }
        }
    }
}