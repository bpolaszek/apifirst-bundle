<?php

namespace BenTools\ApiFirstBundle\Model;

use BenTools\ApiFirstBundle\Form\ApiFirstDeleteType;
use Doctrine\Common\Persistence\ObjectRepository;
use M6Web\Bundle\ApiExceptionBundle\Exception\ValidationFormException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface ResourceHandlerInterface {

    /**
     * @return string
     */
    public function getObjectClass();

    /**
     * @return string
     */
    public function getFormClass();

    /**
     * @return ObjectRepository
     */
    public function getRepository() : ObjectRepository;

    /**
     * @return array|ResourceInterface[]
     */
    public function getObjects();

    /**
     * @param ResourceInterface|null $resource
     * @return FormInterface
     */
    public function getCreationForm(ResourceInterface $resource = null): FormInterface;

    /**
     * @param ResourceInterface $resource
     * @param bool              $clearMissing
     * @param array             $options
     * @return FormInterface
     */
    public function getEditionForm(ResourceInterface $resource, $clearMissing = true, array $options = []) : FormInterface;

    /**
     * @param ResourceInterface $resource
     * @param                   $action
     * @param array             $options
     * @return FormInterface
     */
    public function getDeletionForm(ResourceInterface $resource, $action, array $options = []) : FormInterface;

    /**
     * @param FormInterface     $form
     * @param ResourceInterface $resource
     * @param Request|mixed     $input
     * @param bool              $clearMissing
     * @param bool              $flush
     * @return ResourceInterface
     * @throws ValidationFormException
     */
    public function submit(FormInterface $form, $input, $clearMissing = true, $flush = true) : ResourceInterface;

}