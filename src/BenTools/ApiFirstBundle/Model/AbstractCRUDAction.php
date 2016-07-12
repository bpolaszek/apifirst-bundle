<?php

namespace BenTools\ApiFirstBundle\Model;

use BenTools\HelpfulTraits\Symfony\RouterAwareTrait;
use M6Web\Bundle\ApiExceptionBundle\Exception\ValidationFormException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AbstractCRUDAction {

    use RouterAwareTrait;

    /**
     * @var ResourceHandler
     */
    protected $resourceHandler;

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param callable      $success
     * @param bool          $flush
     * @return ResourceInterface|FormInterface
     */
    public function submitForm(FormInterface $form, Request $request, callable $success = null, bool $flush = true) {
        try {
            $resource = $this->resourceHandler->submit($form, $request, $this->shouldClearMissing($request), $flush);

            if (is_null($success)) {
                return $form->createView();
            }
            if (is_callable($success)) {
                return $success($request, $resource, $form);
            }
            else {
                return $success;
            }

        }
        catch (ValidationFormException $e) {
            return $e->getForm();
        }
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function shouldClearMissing(Request $request) {
        return $request->isMethod('PUT');
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isFormRequest(Request $request) {
        return $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH') || $request->isMethod('DELETE');
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @return bool
     */
    public function shouldHandleForm(FormInterface $form, Request $request) {
        return $request->isMethod($form->getConfig()->getMethod());
    }

    /**
     * @return \Closure
     */
    public function onCreationSuccess() : callable {
        return function (Request $request, ResourceInterface $resource) {
            return $resource;
        };
    }

    /**
     * @return \Closure
     */
    public function onEditionSuccess() : callable {
        return function (Request $request, ResourceInterface $resource) {
            return $resource;
        };
    }

    /**
     * @return \Closure
     */
    public function onDeletionSuccess() : callable {
        return function (Request $request, ResourceInterface $resource) {
            return $resource;
        };
    }

}