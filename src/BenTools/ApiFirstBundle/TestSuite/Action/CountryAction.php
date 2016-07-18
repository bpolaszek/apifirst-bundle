<?php

namespace BenTools\ApiFirstBundle\TestSuite\Action;
use BenTools\ApiFirstBundle\Model\AbstractCRUDAction;
use BenTools\ApiFirstBundle\Model\PreResponse;
use BenTools\ApiFirstBundle\TestSuite\Handler\CountryHandler;
use BenTools\ApiFirstBundle\TestSuite\Model\Country;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class CountryAction
 * @package BenTools\ApiFirstBundle\TestSuite\Action
 * @Route("/countries")
 */
class CountryAction extends AbstractCRUDAction {

    /**
     * CountryAction constructor.
     * @param CountryHandler  $resourceHandler
     * @param RouterInterface $router
     */
    public function __construct(CountryHandler $resourceHandler, RouterInterface $router) {
        $this->resourceHandler = $resourceHandler;
        $this->router          = $router;
    }

    /**
     * @Route("", name="country_list")
     * @View(templateVar="countries")
     * @param Request $request
     * @return array|ResourceInterface|FormInterface
     */
    public function indexAction(Request $request) {
        if ($this->isFormRequest($request)) {

            $form = $this->resourceHandler->getCreationForm();

            if ($this->shouldHandleForm($form, $request)) {
                return $this->submitForm($form, $request, $this->onCreationSuccess());
            }

        }

        return $this->resourceHandler->getObjects();
    }

    /**
     * @Route("/create", name="country_create")
     * @View(templateVar="form")
     * @return \Symfony\Component\Form\FormView
     */
    public function createAction(Request $request) {

        $form = $this->resourceHandler->getCreationForm();

        if ($this->shouldHandleForm($form, $request)) {
            return $this->submitForm($form, $request, $this->onCreationSuccess());
        }

        return $form->createView();
    }

    /**
     * @Route("/{country}", name="country_delete", methods={"DELETE"})
     * @View(templateVar="form")
     * @param Request $request
     * @param Country $country
     * @return ResourceInterface|FormInterface|\Symfony\Component\Form\FormView
     */
    public function deleteAction(Request $request, Country $country) {
        $form = $this->resourceHandler->getDeletionForm($country, $this->generateUrl('country_view', [
            'country' => $country->getId(),
        ]));

        if ($this->isFormRequest($request)) {
            if ($this->shouldHandleForm($form, $request)) {
                return $this->submitForm($form, $request, $this->onRemovalSuccess());
            }
        }
        return $form->createView();
    }


    /**
     * @Route("/{country}", name="country_view")
     * @View(templateVar="country")
     * @param Request $request
     * @param Country $country
     * @return Country
     */
    public function viewAction(Request $request, Country $country) {
        if ($this->isFormRequest($request)) {
            $form = $this->resourceHandler->getEditionForm($country, $this->shouldClearMissing($request));

            if ($this->shouldHandleForm($form, $request)) {
                return $this->submitForm($form, $request, $this->onEditionSuccess());
            }
        }
        return $country;
    }

    /**
     * @Route("/{country}/edit", name="country_edit")
     * @View(templateVar="form")
     * @param Request $request
     * @param Country $country
     * @return ResourceInterface|FormInterface|\Symfony\Component\Form\FormView
     */
    public function editAction(Request $request, Country $country) {
        $form = $this->resourceHandler->getEditionForm($country, $this->shouldClearMissing($request));

        if ($this->shouldHandleForm($form, $request)) {
            return $this->submitForm($form, $request, $this->onEditionSuccess());
        }

        return $form->createView();
    }

    /**
     * @return \Closure
     */
    public function onCreationSuccess() : callable {
        return function (Request $request, Country $country) {
            $UILocation = $this->router->generate('country_list');
            $APILocation = $this->router->generate('country_view', [
                'country' => $country->getId(),
            ]);
            return PreResponse::create($APILocation, $APILocation, Response::HTTP_CREATED)->addFlash('success', 'Creation success!');
        };
    }

    /**
     * @return \Closure
     */
    public function onEditionSuccess() : callable {
        return function (Request $request, Country $country) {
            $UILocation = $this->router->generate('country_list');
            $APILocation = $this->router->generate('country_view', [
                'country' => $country->getId(),
            ]);
            return PreResponse::create($APILocation, $APILocation, Response::HTTP_NO_CONTENT)->addFlash('success', 'Edition success!');
        };
    }



    public function onRemovalSuccess() : callable {
        return function (Request $request, Country $country) {
            $APILocation = $this->router->generate('country_list');
            return PreResponse::create($APILocation, $APILocation, Response::HTTP_NO_CONTENT)->addFlash('success', 'Removal success!');
        };
    }

}