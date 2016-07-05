<?php

namespace BenTools\ApiFirstBundle\Form;

use BenTools\ApiFirstBundle\Services\ApiConsumerDetector;
use Symfony\Component\Form\FormFactoryInterface;

class ApiFirstFormTypeFactory implements FormTypeFactoryInterface {

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ApiConsumerDetector
     */
    private $apiConsumerDetector;

    /**
     * ApiFirstFormTypeFactory constructor.
     * @param FormFactoryInterface $formFactory
     * @param ApiConsumerDetector  $apiConsumerDetector
     */
    public function __construct(FormFactoryInterface $formFactory, ApiConsumerDetector $apiConsumerDetector) {
        $this->formFactory         = $formFactory;
        $this->apiConsumerDetector = $apiConsumerDetector;
    }

    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory() : FormFactoryInterface {
        return $this->formFactory;
    }

    /**
     * @inheritDoc
     */
    public function create($type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = []) {
        return $this->shouldBehaveForAnApi() ? $this->formFactory->createNamed('', $type, $data, $options) : $this->formFactory->create($type, $data, $options);
    }

    /**
     * @inheritDoc
     */
    public function shouldBehaveForAnApi() : bool {
        return $this->apiConsumerDetector->looksLikeAnApiRequest();
    }
}