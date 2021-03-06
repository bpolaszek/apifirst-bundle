<?php

namespace BenTools\ApiFirstBundle\TestSuite\Handler;

use BenTools\ApiFirstBundle\Model\AbstractResourceHandler;
use BenTools\ApiFirstBundle\Model\ResourceHandlerInterface;
use BenTools\ApiFirstBundle\TestSuite\Form\CityType;
use BenTools\ApiFirstBundle\TestSuite\Model\City;

class CityHandlerAbstract extends AbstractResourceHandler implements ResourceHandlerInterface {

    /**
     * @inheritDoc
     */
    public function getObjectClass() : string {
        return City::class;
    }

    /**
     * @inheritDoc
     */
    public function getFormClass() : string {
        return CityType::class;
    }

    /**
     * @inheritDoc
     */
    public function getActionClass() : string {
        return CityType::class;
    }

}