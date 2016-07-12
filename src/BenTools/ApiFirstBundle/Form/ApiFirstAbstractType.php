<?php

namespace BenTools\ApiFirstBundle\Form;

use BenTools\ApiFirstBundle\Services\ApiConsumerDetector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class ApiFirstAbstractType extends AbstractType {

    /**
     * @var ApiConsumerDetector
     */
    private $apiConsumerDetector;

    /**
     * ApiFirstAbstractType constructor.
     * @param ApiConsumerDetector $apiConsumerDetector
     */
    public function __construct(ApiConsumerDetector $apiConsumerDetector) {
        $this->apiConsumerDetector = $apiConsumerDetector;
    }

    /**
     * @return bool
     */
    protected function shouldEnableCSRFProtection() {
        return !$this->apiConsumerDetector->looksLikeAnApiRequest();
    }

    /**
     * @return bool
     */
    public function looksLikeAnApiRequest() {
        return $this->apiConsumerDetector->looksLikeAnApiRequest();
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @return bool
     */
    public function isACreationForm(FormBuilderInterface $formBuilder) {
        return $formBuilder->getMethod() == 'POST';
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @return bool
     */
    public function isAnEditionForm(FormBuilderInterface $formBuilder) {
        return in_array($formBuilder->getMethod(), ['PUT', 'PATCH']);
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @return bool
     */
    public function isADeletionForm(FormBuilderInterface $formBuilder) {
        return $formBuilder->getMethod() == 'DELETE';
    }
}