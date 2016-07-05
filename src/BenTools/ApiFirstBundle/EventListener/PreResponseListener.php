<?php

namespace BenTools\ApiFirstBundle\EventListener;

use BenTools\ApiFirstBundle\Model\PreResponse;
use BenTools\ApiFirstBundle\Services\ApiConsumerDetector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PreResponseListener implements EventSubscriberInterface {
    
    /**
     * @var ApiConsumerDetector
     */
    private $apiConsumerDetector;

    /**
     * PreResponseListener constructor.
     * @param ApiConsumerDetector $apiConsumerDetector
     */
    public function __construct(ApiConsumerDetector $apiConsumerDetector) {
        $this->apiConsumerDetector = $apiConsumerDetector;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event) {

        $result = $event->getControllerResult();

        if ($result instanceof PreResponse) {

            # Case of an API Request
            if ($this->apiConsumerDetector->looksLikeAnApiRequest()) {

                $event->setResponse(new Response('', $result->getAPIStatusCode(), [
                    'Location' => $result->getAPILocation(),
                ]));
            }

            # Case of a UI request
            else {

                $request = $event->getRequest();

                if ($request->hasSession() && $result->getFlashes()) {

                    $session = $request->getSession();
                    try {
                        foreach ($result->getFlashes() AS $type => $flashes) {
                            foreach ($flashes AS $message) {
                                $session->getBag('flashes')->add($type, $message);
                            }
                        }
                    }
                    catch (\InvalidArgumentException $e) {
                    }

                }

                $event->setResponse(new RedirectResponse($result->getUILocation()));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            KernelEvents::VIEW => [
                'onKernelView',
                50
            ],
        ];
    }

}