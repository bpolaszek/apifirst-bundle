<?php

namespace BenTools\ApiFirstBundle\Model;

use BenTools\ApiFirstBundle\Form\ApiFirstDeleteType;
use BenTools\ApiFirstBundle\Form\FormTypeFactoryInterface;
use BenTools\ApiFirstBundle\Services\ApiConsumerDetector;
use BenTools\HelpfulTraits\Symfony\EntityManagerAwareTrait;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use M6Web\Bundle\ApiExceptionBundle\Exception\ValidationFormException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class ResourceHandler implements ResourceHandlerInterface {

    use EntityManagerAwareTrait;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FormTypeFactoryInterface
     */
    protected $formTypeFactory;

    /**
     * @var ApiConsumerDetector
     */
    private $apiConsumerDetector;

    /**
     * ContactResourceHandler constructor.
     * @param FormFactoryInterface     $formFactory
     * @param FormTypeFactoryInterface $formTypeFactory
     * @param ManagerRegistry          $managerRegistry
     * @param ApiConsumerDetector      $apiConsumerDetector
     */
    public function __construct(FormFactoryInterface $formFactory,
                                FormTypeFactoryInterface $formTypeFactory,
                                ManagerRegistry $managerRegistry,
                                ApiConsumerDetector $apiConsumerDetector) {
        $this->formFactory         = $formFactory;
        $this->formTypeFactory     = $formTypeFactory;
        $this->managerRegistry     = $managerRegistry;
        $this->apiConsumerDetector = $apiConsumerDetector;
    }

    /**
     * @inheritDoc
     */
    public function getObjects() {
        return $this->getRepository()->findAll();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCreationForm(ResourceInterface $resource = null, array $options = []) : FormInterface {
        $formClass   = $this->getFormClass();
        $objectClass = $this->getObjectClass();

        if (!$resource) {
            $resource = new $objectClass;
        }

        if (!$resource instanceof $objectClass) {
            throw new UnexpectedTypeException($resource, $objectClass);
        }

        return $this->formTypeFactory->create($formClass, $resource, $options);
    }

    /**
     * @param ResourceInterface $resource
     * @return FormInterface
     */
    public function getEditionForm(ResourceInterface $resource, $clearMissing = true, array $options = []) : FormInterface {
        $formClass   = $this->getFormClass();
        $objectClass = $this->getObjectClass();

        if (!$resource instanceof $objectClass) {
            throw new UnexpectedTypeException($resource, $objectClass);
        }

        return $this->formTypeFactory->create($formClass, $resource, array_replace([
            'method' => $clearMissing ? 'PUT' : 'PATCH',
        ], $options));
    }

    /**
     * @param ResourceInterface $resource
     * @return FormInterface
     */
    public function getDeletionForm(ResourceInterface $resource, $action, array $options = []) : FormInterface {
        $formClass   = $this->getFormClass();
        $objectClass = $this->getObjectClass();

        if (!$resource instanceof $objectClass) {
            throw new UnexpectedTypeException($resource, $objectClass);
        }

        return $this->formTypeFactory->create($formClass, $resource, array_replace([
            'method' => 'DELETE',
            'action' => $action,
        ], $options));
    }

    /**
     * @param FormInterface     $form
     * @param ResourceInterface $resource
     * @param Request|mixed     $input
     * @param bool              $clearMissing
     * @param bool              $flush
     * @return ResourceInterface
     * @throws ValidationFormException
     */
    public function submit(FormInterface $form, $input, $clearMissing = true, $flush = true) : ResourceInterface {

        # UI form - rely on exactly what has been posted
        if ($input instanceof Request && !$this->apiConsumerDetector->looksLikeAnApiRequest()) {
            $form->handleRequest($input);
        }

        # API form - don't rely on automatic $clearMissing since some values may be set by default with the factory
        elseif ($input instanceof Request) {
            if (in_array($input->getMethod(), ['PUT', 'PATCH']) && !in_array($input->getContentType(), ['form', 'json'])) {
                throw new BadRequestHttpException("Input should be x-www-form-urlencoded or raw json.");
            }
            $form->submit($input->request->all(), $clearMissing);
        }

        # Other kind of data, like array, etc
        else {
            $form->submit($input, $clearMissing);
        }

        if ($form->isValid()) {

            $resource = $form->getData();

            if ($form->getConfig()->getMethod() == 'DELETE') {
                $this->getEntityManagerOf($resource)->remove($resource);
            }
            else {
                $this->getEntityManagerOf($resource)->persist($resource);
            }

            if ($flush) {
                $this->getEntityManagerOf($resource)->flush($resource);
            }

            return $resource;
        }
        else {
            throw new ValidationFormException($form);
        }
    }

    /**
     * @inheritDoc
     */
    public function getRepository() : ObjectRepository {
        return $this->getEntityManagerOf($this->getObjectClass())->getRepository($this->getObjectClass());
    }
}