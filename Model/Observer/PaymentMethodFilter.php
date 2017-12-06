<?php
namespace Company\Module\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Model\Ui\EbanxBoletoConfigProvider;

class PaymentMethodAvailable implements ObserverInterface
{
    /**
     * @var Api
     */
    private $api;

    /**
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $country = $observer
            ->getCustomerAddress()
            ->getCustomer()
            ->getDefaultBillingAddress()
            ->getCountry();

        $checkResult = $observer->getEvent()->getResult();
        $checkResult->setData(
            'is_available',
            $this->api->isAvailableForCountry(
                $observer->getEvent()->getMethodInstance()->getCode(),
                $country
            )
        );

        return $this;
    }
}
