<?php

namespace BenTools\ApiFirstBundle\Services;

use BenTools\ApiFirstBundle\Model\AbstractResourceAction;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ResourceHelpersExtension extends \Twig_Extension implements ContainerAwareInterface {

    use ContainerAwareTrait;

    /**
     * @var ResourceHandlerRegistry
     */
    private $resourceHandlerRegistry;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ResourceHelpersExtension constructor.
     * @param ResourceHandlerRegistry $resourceHandlerRegistry
     * @param RequestStack            $requestStack
     * @param ContainerInterface      $container
     */
    public function __construct(ResourceHandlerRegistry $resourceHandlerRegistry, RequestStack $requestStack, ContainerInterface $container) {
        $this->resourceHandlerRegistry = $resourceHandlerRegistry;
        $this->container               = $container;
        $this->requestStack            = $requestStack;

    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('resourcePath', [$this, 'getResourcePath']),
            new \Twig_SimpleFunction('resourceAction', [$this, 'getActionReference']),
        ];
    }

    public function getActionReference(ResourceInterface $resource, $action) {
        $actionService   = $this->getActionService($resource);
        $attributes      = $actionService->resolveResourcePathParams($this->requestStack->getMasterRequest(), $resource);
        $actionReference = $this->container->get('twig')->getExtension('http_kernel')->controller(sprintf('%s:%sAction', strtolower($this->getActionClass($resource)), $action), $attributes);
        return $actionReference;
    }

    /**
     * @param ResourceInterface $resource
     * @return string
     */
    public function getResourcePath(ResourceInterface $resource, $path) {
        $actionService = $this->getActionService($resource);
        $request       = $this->requestStack->getMasterRequest();
        switch ($path) {
            case 'index':
                return $actionService->getIndexPath($request, $resource);
            case 'create':
                return $actionService->getCreatePath($request, $resource);
            case 'view':
                return $actionService->getViewPath($request, $resource);
            case 'edit':
                return $actionService->getEditPath($request, $resource);
        }

        if (is_callable([$actionService, sprintf('get%sPath', ucfirst($path))])) {
            return call_user_func([$actionService, sprintf('get%sPath', ucfirst($path))], $request, $resource);
        }
        else {
            throw new \RuntimeException(sprintf("Unable to find path %s for resource %s", $path, get_class($resource)));
        }
    }

    /**
     * @param ResourceInterface $resource
     * @return object|AbstractResourceAction
     */
    public function getActionService(ResourceInterface $resource) {
        $actionClass = $this->getActionClass($resource);
        return $this->container->get(strtolower($actionClass));
    }

    /**
     * @param ResourceInterface $resource
     * @return string
     */
    public function getActionClass(ResourceInterface $resource) {
        return $this->resourceHandlerRegistry->getResourceHandler($resource)->getActionClass();
    }

    /**
     * @inheritDoc
     */
    public function getName() {
        return __CLASS__;
    }

}