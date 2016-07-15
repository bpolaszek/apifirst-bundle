<?php

namespace BenTools\ApiFirstBundle\Model;

use Symfony\Component\HttpFoundation\Response;

class PreResponse {

    /**
     * @var string
     */
    private $UILocation;

    /**
     * @var string
     */
    private $APILocation;

    /**
     * @var int
     */
    private $APIStatusCode;

    private $flashes = [];

    /**
     * PreResponse constructor.
     * @param string $UILocation
     * @param string $APILocation
     * @param int    $APIStatusCode
     */
    public function __construct(string $UILocation, string $APILocation, int $APIStatusCode = Response::HTTP_NO_CONTENT) {
        $this->UILocation    = $UILocation;
        $this->APILocation   = $APILocation;
        $this->APIStatusCode = $APIStatusCode;
    }

    /**
     * @return string
     */
    public function getUILocation() {
        return $this->UILocation;
    }

    /**
     * @param string $UILocation
     * @return $this - Provides Fluent Interface
     */
    public function setUILocation($UILocation) {
        $this->UILocation = $UILocation;
        return $this;
    }

    /**
     * @return string
     */
    public function getAPILocation() {
        return $this->APILocation;
    }

    /**
     * @param string $APILocation
     * @return $this - Provides Fluent Interface
     */
    public function setAPILocation($APILocation) {
        $this->APILocation = $APILocation;
        return $this;
    }

    /**
     * @return int
     */
    public function getAPIStatusCode() {
        return $this->APIStatusCode;
    }

    /**
     * @param int $APIStatusCode
     * @return $this - Provides Fluent Interface
     */
    public function setAPIStatusCode($APIStatusCode) {
        $this->APIStatusCode = $APIStatusCode;
        return $this;
    }

    /**
     * @param                       $type
     * @param                       $message
     * @return $this
     */
    public function addFlash($type, $message) {
        if (!array_key_exists($type, $this->flashes)) {
            $this->flashes[$type] = [$message];
        }
        else {
            $this->flashes[$type][] = $message;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getFlashes() {
        return $this->flashes;
    }

    /**
     * @param string $UILocation
     * @param string $APILocation
     * @param int    $APIStatusCode
     * @return static|PreResponse
     */
    public static function create(string $UILocation, string $APILocation, int $APIStatusCode = Response::HTTP_OK) {
        return new static($UILocation, $APILocation, $APIStatusCode);
    }
}