<?php
namespace Ebanx\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Ebanx\Payments\Helper\Data as EbanxData;

class MerchantAccountDataBuilder implements BuilderInterface
{
    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;

    /**
     * RecurringDataBuilder constructor.
     *
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     */
    public function __construct(
        EbanxData $ebanxHelper
    ) {
        $this->ebanxHelper = $ebanxHelper;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);
        $order = $paymentDataObject->getOrder();
        $storeId = $order->getStoreId();

//        TODO: set integration keys (live or sandbox)
        $ebanxIntegrationKeys = ["integration_key" => $this->ebanxHelper->getEbanxAbstractConfigData("integration_key_sandbox", $storeId),
            "integration_key_public" => $this->ebanxHelper->getEbanxAbstractConfigData("integration_key_public_sandbox", $storeId)];

        return ["ebanxIntegrationKeys" => $ebanxIntegrationKeys];
    }
}