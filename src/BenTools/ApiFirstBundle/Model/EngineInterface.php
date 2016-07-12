<?php

namespace BenTools\ApiFirstBundle\Model;

interface EngineInterface extends \Symfony\Component\Templating\EngineInterface {

    /**
     * @param       $key
     * @param       $value
     * @param array $options
     * @return $this
     */
    public function assign($key, $value, $options = []);

}