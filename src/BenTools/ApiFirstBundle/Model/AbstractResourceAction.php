<?php

namespace BenTools\ApiFirstBundle\Model;

use BenTools\HelpfulTraits\Symfony\ControllerHelpersTrait;
use BenTools\HelpfulTraits\Symfony\RouterAwareTrait;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractResourceAction extends AbstractCRUDAction {

    use RouterAwareTrait, ControllerHelpersTrait;

    const ENTITY_NAME          = null;
    const PLURAL_ENTITY_NAME   = null;
    const ENTITY_SLUG_NAME     = null;
    const LISTING_ROUTE        = null;
    const SINGLE_ENTITY_ROUTE  = null;
    const CREATE_FORM_ROUTE    = null;
    const EDIT_FORM_ROUTE      = null;
    const RESOURCE_PATH_PARAMS = [
        'id' => 'self.id',
    ];

    /**
     * @var AbstractResourceHandler
     */
    protected $resourceHandler;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * TrackingSoftwareAction constructor.
     * @param ResourceHandlerInterface $resourceHandler
     * @param ManagerRegistry          $managerRegistry
     * @param RouterInterface          $router
     * @param EngineInterface          $templating
     */
    public function __construct(RouterInterface $router, EngineInterface $templating) {
        $this->router     = $router;
        $this->templating = $templating;
    }

    /**
     * @param Request           $request
     * @param ResourceInterface $resource
     * @return FormInterface
     */
    public function getCreationForm(Request $request, ResourceInterface $resource) {
        return $this->resourceHandler->getCreationForm($resource);
    }

    /**
     * @param Request           $request
     * @param ResourceInterface $resource
     * @return FormInterface
     */
    public function getEditionForm(Request $request, ResourceInterface $resource) {
        return $this->resourceHandler->getEditionForm($resource);
    }

    /**
     * @param Request           $request
     * @param ResourceInterface $resource
     */
    public function getDeletionForm(Request $request, ResourceInterface $resource) {
        return $this->resourceHandler->getDeletionForm($resource, $this->getViewPath($request, $resource));
    }

    /**
     * @inheritDoc
     */
    public function submitForm(FormInterface $form, Request $request, callable $success = null, bool $flush = true) {
        switch (true) {
            case is_callable($success):
                break;
            case $this->isADeletionForm($form):
                $success = $this->onDeletionSuccess();
                break;
            case $this->isAnEditionForm($form):
                $success = $this->onEditionSuccess();
                break;
            case $this->isACreationForm($form):
                $success = $this->onCreationSuccess();
                break;
            default:
                $success = null;
        }
        return parent::submitForm($form, $request, $success, $flush);
    }

    /**
     * @inheritDoc
     */
    public function onCreationSuccess() : callable {
        return function (Request $request, ResourceInterface $trackingSoftware) {
            $UILocation  = $this->getIndexPath($request, $trackingSoftware);
            $APILocation = $this->getViewPath($request, $trackingSoftware);
            return PreResponse::create($UILocation, $APILocation, Response::HTTP_CREATED);
        };
    }

    /**
     * @inheritDoc
     */
    public function onEditionSuccess() : callable {
        return function (Request $request, ResourceInterface $trackingSoftware) {
            $UILocation  = $this->getIndexPath($request, $trackingSoftware);
            $APILocation = $this->getViewPath($request, $trackingSoftware);
            return PreResponse::create($UILocation, $APILocation, Response::HTTP_NO_CONTENT)->addFlash('success', 'Edition success!');
        };
    }

    /**
     * @inheritDoc
     */
    public function onDeletionSuccess() : callable {
        return function (Request $request, ResourceInterface $trackingSoftware) {
            $UILocation = $this->getIndexPath($request, $trackingSoftware);
            return PreResponse::create($UILocation, '', Response::HTTP_NO_CONTENT)->addFlash('success', 'Removal success!');
        };
    }

    /**
     * @inheritDoc
     */
    public function getEntitySlugName() {
        if (static::ENTITY_SLUG_NAME === null)
            throw new \LogicException("ENTITY_SLUG_NAME constant should be overloaded.");
        return static::ENTITY_SLUG_NAME;
    }

    /**
     * @return string
     */
    public function getEntityName() {
        if (static::ENTITY_NAME === null)
            throw new \LogicException("ENTITY_NAME constant should be overloaded.");
        return static::ENTITY_NAME;
    }

    /**
     * @return string
     */
    public function getPluralEntityName() {
        if (static::PLURAL_ENTITY_NAME === null)
            throw new \LogicException("PLURAL_ENTITY_NAME constant should be overloaded.");
        return static::PLURAL_ENTITY_NAME;
    }

    /**
     * @return string
     */
    public function getListingRoute() {
        if (static::LISTING_ROUTE === null)
            throw new \LogicException("LISTING_ROUTE constant should be overloaded.");
        return static::LISTING_ROUTE;
    }

    /**
     * @return string
     */
    public function getCreateFormRoute() {
        if (static::CREATE_FORM_ROUTE === null)
            throw new \LogicException("CREATE_FORM_ROUTE constant should be overloaded.");
        return static::CREATE_FORM_ROUTE;
    }

    /**
     * @return string
     */
    public function getSingleEntityRoute() {
        if (static::SINGLE_ENTITY_ROUTE === null)
            throw new \LogicException("SINGLE_ENTITY_ROUTE constant should be overloaded.");
        return static::SINGLE_ENTITY_ROUTE;
    }

    /**
     * @return string
     */
    public function getEditFormRoute() {
        if (static::EDIT_FORM_ROUTE === null)
            throw new \LogicException("EDIT_FORM_ROUTE constant should be overloaded.");
        return static::EDIT_FORM_ROUTE;
    }

    /**
     * @param ResourceInterface $resource
     * @return string
     */
    public function getIndexPath(Request $request, ResourceInterface $resource) : string {
        return $this->generateUrl($this->getListingRoute(), $this->resolveResourcePathParams($request, $resource));
    }

    /**
     * @param ResourceInterface $trackingSoftware
     * @return string
     */
    public function getViewPath(Request $request, ResourceInterface $resource) : string {
        return $this->generateUrl($this->getSingleEntityRoute(), $this->resolveResourcePathParams($request, $resource));
    }

    /**
     * @inheritDoc
     */
    public function getCreatePath(Request $request, ResourceInterface $resource) : string {
        return $this->generateUrl($this->getCreateFormRoute(), $this->resolveResourcePathParams($request, $resource));
    }

    /**
     * @param ResourceInterface $trackingSoftware
     * @return string
     */
    public function getEditPath(Request $request, ResourceInterface $resource) : string {
        return $this->generateUrl($this->getEditFormRoute(), $this->resolveResourcePathParams($request, $resource));
    }

    /**
     * @return array
     */
    public function getResourcePathParams() {
        if (!is_array(static::RESOURCE_PATH_PARAMS))
            throw new \LogicException("RESOURCE_PATH_PARAMS constant should be overloaded.");
        return static::RESOURCE_PATH_PARAMS;
    }

    /**
     * @param Request           $request
     * @param ResourceInterface $resource
     * @return array
     */
    public function resolveResourcePathParams(Request $request, ResourceInterface $resource) {
        $language = new ExpressionLanguage();
        $params   = array_map(function ($expression) use ($language, $resource, $request) {
            return $language->evaluate($expression, array_merge($request->attributes->all(), [
                $this->getEntitySlugName() => $resource,
                'self' => $resource,
            ]));
        }, $this->getResourcePathParams());

        return $params;

    }

}