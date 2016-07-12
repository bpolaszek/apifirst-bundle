<?php

namespace BenTools\ApiFirstBundle\TestSuite\Handler;

use BenTools\ApiFirstBundle\Model\ResourceHandler;
use BenTools\ApiFirstBundle\Model\ResourceHandlerInterface;
use BenTools\ApiFirstBundle\TestSuite\Form\CountryType;
use BenTools\ApiFirstBundle\TestSuite\Model\Country;

class CountryHandler extends ResourceHandler implements ResourceHandlerInterface {

    /**
     * @inheritDoc
     */
    public function getObjectClass() {
        return Country::class;
    }

    /**
     * @inheritDoc
     */
    public function getFormClass() {
        return CountryType::class;
    }

}