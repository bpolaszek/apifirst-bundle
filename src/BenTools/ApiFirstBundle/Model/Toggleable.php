<?php

namespace BenTools\ApiFirstBundle\Model;

interface Toggleable {

    /**
     * @inheritDoc
     */
    public function enable();

    /**
     * @inheritDoc
     */
    public function disable();

    /**
     * @param boolean $enabled
     * @return $this - Provides Fluent Interface
     */
    public function setEnabled($enabled);

    /**
     * @inheritDoc
     */
    public function isEnabled();

}