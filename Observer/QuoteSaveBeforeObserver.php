<?php


namespace Ambientia\CollectorSubscriptionFramework\Observer;


use Ambientia\CollectorSubscriptionFramework\Api\SubscriptionDataResolverInterface;
use Customweb_Collector_Constant_Form;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\PaymentInterface;

class QuoteSaveBeforeObserver implements ObserverInterface
{

    /**
     * @var SubscriptionDataResolverInterface
     */
    private $subscriptionDataResolver;

    /**
     * QuoteObserver constructor.
     * @param SubscriptionDataResolverInterface $subscriptionDataResolver
     */
    public function __construct(SubscriptionDataResolverInterface $subscriptionDataResolver)
    {
        $this->subscriptionDataResolver = $subscriptionDataResolver;
    }

    /**
     * This observer saves the customer SSN and the condition checkbox values to the order payment
     * for later use. Collector Direct payment method uses the values on authorize / AddInvoice API call.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->subscriptionDataResolver->isSubscription()) {
            return;
        }
        /** @var CartInterface $quote */
        $quote = $observer->getEvent()->getData('quote');
        /** @var PaymentInterface $payment */
        $payment = $quote->getPayment();
        if ($payment->getMethod() != 'collectorcw_collectordirect') {
            return;
        }
        $ssn = $this->subscriptionDataResolver->getSsn();
        $conditionCheckbox = $this->subscriptionDataResolver->getConditionCheckbox();
        $payment->setAdditionalInformation(Customweb_Collector_Constant_Form::SSN, $ssn);
        $payment->setAdditionalInformation(Customweb_Collector_Constant_Form::CONDITION_CHECKBOX, $conditionCheckbox);
    }
}
