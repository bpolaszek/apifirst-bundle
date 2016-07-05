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
    public function __construct(FormatNegotiator $formatNegotiator) {
        $this->formatNegotiator     = $formatNegotiator;
    }

    /**
     * @return \Negotiation\Accept|\Negotiation\AcceptHeader|null|void
     * @throws \FOS\RestBundle\Util\StopFormatListenerException
     */
    public function looksLikeAnApiRequest() {
        return in_array($this->formatNegotiator->getBest(null)->getType(), ['application/json', 'text/xml']);
    }

}