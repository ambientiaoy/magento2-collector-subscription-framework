<?php


namespace Ambientia\CollectorSubscriptionFramework\Api;

/**
 * This interface is responsible of providing the data that is needed for authorizing a new invoice
 * by Collector Direct payment method.
 *
 * Interface SubscriptionDataResolverInterface
 * @package Ambientia\CollectorSubscriptionFramework\Api
 */
interface SubscriptionDataResolverInterface
{
    /**
     * Return value indicates the app state, if the process is currently handling a subscription.
     *
     * @return bool
     */
    public function isSubscription(): bool;

    /**
     * Returns the Social Security Number for the currently handled subscription.
     * The validation of payment authorization expects a valid string.
     *
     * @return string|null
     */
    public function getSsn(): ?string;

    /**
     * Returns if the customer has approved the conditions for the currently handled subscription.
     * Must return true to pass the validation. Otherwise the payment authorization fails.
     *
     * @return bool
     */
    public function getConditionCheckbox(): bool;
}
