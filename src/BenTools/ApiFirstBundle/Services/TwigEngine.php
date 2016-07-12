<?php

namespace BenTools\ApiFirstBundle\Services;

use BenTools\ApiFirstBundle\Model\EngineInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class TwigEngine implements EngineInterface {

    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var \Symfony\Bridge\Twig\TwigEngine
     */
    private $engine;

    /**
     * TwigEngine constructor.
     * @param \Twig_Environment               $environment
     * @param \Symfony\Bridge\Twig\TwigEngine $engine
     */
    public function __construct(\Twig_Environment $environment, \Symfony\Bridge\Twig\TwigEngine $engine) {
        $this->environment = $environment;
        $this->engine      = $engine;
    }

    /**
     * @inheritDoc
     */
    public function supports($name) {
        return $this->engine->supports($name);
    }

    /**
     * @inheritDoc
     */
    public function assign($key, $value, $options = []) {
        $this->environment->addGlobal($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function render($name, array $parameters = []) {
        return $this->engine->render($name, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function exists($name) {
        return $this->engine->exists($name);
    }
}