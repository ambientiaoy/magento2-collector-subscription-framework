<?php

namespace Ambientia\CollectorSubscriptionFramework\Plugin\Model\Authorization\Method;

use Ambientia\CollectorSubscriptionFramework\Api\SubscriptionDataResolverInterface;

class Server
{
    /**
     * @var SubscriptionDataResolverInterface
     */
    private $subscriptionDataResolver;

    /**
     * Server constructor.
     * @param SubscriptionDataResolverInterface $subscriptionDataResolver
     */
    public function __construct(SubscriptionDataResolverInterface $subscriptionDataResolver)
    {
        $this->subscriptionDataResolver = $subscriptionDataResolver;
    }

    /**
     * @param \Customweb\CollectorCw\Model\Authorization\Method\Server $subject
     * @param callable $proceed
     */
    public function aroundStartAuthorization(\Customweb\CollectorCw\Model\Authorization\Method\Server $subject, callable $proceed)
    {
        $response = $subject->processAuthorization();
        $wrapper = new \Customweb_Core_Http_Response($response);
        $wrapper->send();
        // Don't die if executed as php cli command
        if (!$this->subscriptionDataResolver->isSubscription()) {
            die();
        }
	}
}
