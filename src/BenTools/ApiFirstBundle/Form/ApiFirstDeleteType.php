<?php

namespace BenTools\ApiFirstBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiFirstDeleteType extends ApiFirstAbstractType {
    
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->setMethod('DELETE');
        $builder->add('id', HiddenType::class);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'csrf_protection' => $this->shouldEnableCSRFProtection(),
        ]);
    }
}