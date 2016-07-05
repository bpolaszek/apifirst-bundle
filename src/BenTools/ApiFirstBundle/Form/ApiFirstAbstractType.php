<?php

namespace BenTools\ApiFirstBundle\Form;

use BenTools\ApiFirstBundle\Services\ApiConsumerDetector;
use Symfony\Component\Form\AbstractType;

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
}