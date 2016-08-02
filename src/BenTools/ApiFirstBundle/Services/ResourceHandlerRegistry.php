<?php

namespace BenTools\ApiFirstBundle\Services;

use BenTools\ApiFirstBundle\Model\ResourceHandlerInterface;

class ResourceHandlerRegistry {

    /**
     * @var ResourceHandlerInterface[]
     */
    protected $resourceHandlers = [];

    /**
     * @param ResourceHandlerInterface $resourceHandler
     * @return $this
     */
    public function registerResourceHandler(ResourceHandlerInterface $resourceHandler) {
        $this->resourceHandlers[$resourceHandler->getObjectClass()] = $resourceHandler;
        return $this;
    }

    /**
     * @param $classOrObject
     * @return ResourceHandlerInterface|\BenTools\ApiFirstBundle\Model\ResourceHandlerInterface[]
     * @throws \OutOfBoundsException
     */
    public function getResourceHandler($classOrObject) {
        $className = $this->resolveClassOrObject($classOrObject);
        if (!isset($this->resourceHandlers[$className])) {
            throw new \OutOfBoundsException(sprintf('Unable to locate resource handler for %s', $className));
        }
        return $this->resourceHandlers[$className];
    }

    /**
     * @param $classOrObject
     * @return string
     */
    private function resolveClassOrObject($classOrObject) {
        switch (true) {
            case is_object($classOrObject) && $classOrObject instanceof \Doctrine\ORM\Proxy\Proxy:
                return get_parent_class($classOrObject);
            case is_object($classOrObject):
                return get_class($classOrObject);
            default:
                return $classOrObject;
        }
    }

}