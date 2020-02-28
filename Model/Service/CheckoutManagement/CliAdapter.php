<?php


namespace Ambientia\CollectorSubscriptionFramework\Model\Service\CheckoutManagement;


use Customweb\CollectorCw\Api\CheckoutManagementInterface;
use Customweb\CollectorCw\Api\Data\AuthorizationDataInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CliAdapter
{
    /**
     * @var CheckoutManagementInterface
     */
    private $checkoutManagement;
    /**
     * @var AuthorizationDataInterface
     */
    private $authorizationData;

    /**
     * CliAdapter constructor.
     * @param CheckoutManagementInterface $checkoutManagement
     * @param AuthorizationDataInterface $authorizationData
     */
    public function __construct(
        CheckoutManagementInterface $checkoutManagement,
        AuthorizationDataInterface $authorizationData
    ) {
        $this->checkoutManagement = $checkoutManagement;
        $this->authorizationData = $authorizationData;
    }

    /**
     * @param OrderInterface $order
     */
    public function authorize(OrderInterface $order)
    {
        if (!isset($_SERVER['REMOTE_ADDR'])) {
            // Executing PHP by cli IP address must be faked
            $_SERVER['REMOTE_ADDR'] = '0.0.0.0';
        }
        $this->authorizationData->setHiddenFormFields($order->getPayment()->getAdditionalInformation());
        $this->checkoutManagement->authorize($order->getId(), $this->authorizationData->getHiddenFormFields());
    }
}
