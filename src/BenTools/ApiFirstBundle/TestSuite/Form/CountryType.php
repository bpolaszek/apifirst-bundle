<?php

namespace BenTools\ApiFirstBundle\TestSuite\Form;

use BenTools\ApiFirstBundle\Form\ApiFirstAbstractType;
use BenTools\ApiFirstBundle\TestSuite\Model\Country;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends ApiFirstAbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        if ($builder->getMethod() == 'DELETE') {
            $builder->add('delete', SubmitType::class);
        }
        else {
            $builder->add('name', TextType::class);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class'      => Country::class,
            'csrf_protection' => $this->shouldEnableCSRFProtection(),
        ]);
    }
}