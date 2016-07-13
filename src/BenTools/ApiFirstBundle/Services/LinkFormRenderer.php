<?php

namespace BenTools\ApiFirstBundle\Services;

use Symfony\Component\Form\FormView;
use Twig_Environment;

class LinkFormRenderer extends \Twig_Extension implements \Twig_Extension_InitRuntimeInterface {

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * LinkFormRenderer constructor.
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * @param FormView $formView
     * @return \Twig_Template
     */
    public function renderFormLink(FormView $formView) {
        return $this->twig->render('BenToolsApiFirstBundle::formlink.html.twig', [
            'form'   => $formView,
        ]);
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('renderFormLink', [$this, 'renderFormLink'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName() {
        return self::class;
    }

}