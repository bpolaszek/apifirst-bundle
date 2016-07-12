<?php

namespace BenTools\ApiFirstBundle\TestSuite\Handler;

use BenTools\ApiFirstBundle\Model\ResourceHandler;
use BenTools\ApiFirstBundle\Model\ResourceHandlerInterface;
use BenTools\ApiFirstBundle\TestSuite\Form\CityType;
use BenTools\ApiFirstBundle\TestSuite\Model\City;

class CityHandler extends ResourceHandler implements ResourceHandlerInterface {

    /**
     * @inheritDoc
     */
    public function getObjectClass() {
        return City::class;
    }

    /**
     * @inheritDoc
     */
    public function getFormClass() {
        return CityType::class;
    }

}