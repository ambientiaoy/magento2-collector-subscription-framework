<?php


namespace Ambientia\CollectorSubscriptionFramework\Observer;

use Ambientia\CollectorSubscriptionFramework\Api\SubscriptionDataResolverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSaveAfterObserver implements ObserverInterface
{
    /**
     * @var SubscriptionDataResolverInterface
     */
    private $subscriptionDataResolver;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * QuoteObserver constructor.
     * @param SubscriptionDataResolverInterface $subscriptionDataResolver
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        SubscriptionDataResolverInterface $subscriptionDataResolver,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->subscriptionDataResolver = $subscriptionDataResolver;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
    }

    /**
     * Set session variables after a new quote was created by the subscription module.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->subscriptionDataResolver->isSubscription()) {
            return;
        }
        $quote = $observer->getEvent()->getData('quote');
        $this->customerSession->setCustomerId($quote->getCustomerId());
        $this->checkoutSession->clearQuote();
        $this->checkoutSession->setQuoteId($quote->getId());
    }
}
