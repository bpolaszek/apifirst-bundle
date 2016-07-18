<?php

namespace BenTools\ApiFirstBundle\TestSuite\Handler;

use BenTools\ApiFirstBundle\Model\ResourceHandler;
use BenTools\ApiFirstBundle\Model\ResourceHandlerInterface;
use BenTools\ApiFirstBundle\TestSuite\Action\CountryAction;
use BenTools\ApiFirstBundle\TestSuite\Form\CountryType;
use BenTools\ApiFirstBundle\TestSuite\Model\Country;

class CountryHandler extends ResourceHandler implements ResourceHandlerInterface {

    /**
     * @inheritDoc
     */
    public function getObjectClass() : string {
        return Country::class;
    }

    /**
     * @inheritDoc
     */
    public function getFormClass() : string {
        return CountryType::class;
    }

    /**
     * @inheritDoc
     */
    public function getActionClass() : string {
        return CountryAction::class;
    }


}