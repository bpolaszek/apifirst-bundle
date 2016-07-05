<?php

namespace BenTools\ApiFirstBundle\TestSuite\Form;

use BenTools\ApiFirstBundle\Form\ApiFirstAbstractType;
use BenTools\ApiFirstBundle\TestSuite\Model\City;
use BenTools\ApiFirstBundle\TestSuite\Model\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityType extends ApiFirstAbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('country', EntityType::class, [
                'class' => Country::class,
            ])
            ->add('name');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class'      => City::class,
            'csrf_protection' => $this->shouldEnableCSRFProtection(),
        ]);
    }

}