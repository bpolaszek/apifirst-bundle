<?php

namespace BenTools\ApiFirstBundle\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

interface FormTypeFactoryInterface {
    
    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory() : FormFactoryInterface;

    /**
     * @param string $type
     * @param null   $data
     * @param array  $options
     * @return FormInterface
     */
    public function create($type = 'Symfony\Component\Form\Extension\Core\Type\FormType', $data = null, array $options = []);

    /**
     * @return bool
     */
    public function shouldBehaveForAnApi() : bool;

}