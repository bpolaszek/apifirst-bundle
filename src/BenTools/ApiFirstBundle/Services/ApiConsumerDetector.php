<?php

namespace BenTools\ApiFirstBundle\Services;

use FOS\RestBundle\Negotiation\FormatNegotiator;

class ApiConsumerDetector {

    /**
     * @var FormatNegotiator
     */
    private $formatNegotiator;

    /**
     * ApiConsumerDetector constructor.
     * @param FormatNegotiator              $formatNegotiator
     */
    public function __construct(FormatNegotiator $formatNegotiator = null) {
        $this->formatNegotiator     = $formatNegotiator;
    }

    /**
     * @return bool
     * @throws \FOS\RestBundle\Util\StopFormatListenerException
     */
    public function looksLikeAnApiRequest() {
        if (null === $this->formatNegotiator) {
            return false;
        }
        return in_array($this->formatNegotiator->getBest(null)->getType(), ['application/json', 'text/xml']);
    }

}