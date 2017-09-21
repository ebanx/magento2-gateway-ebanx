<?php
namespace Ebanx\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Payment Data Builder
 */
class PaymentDataBuilder implements BuilderInterface
{

    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;

    /**
     * PaymentDataBuilder constructor.
     *
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     */
    public function __construct(EbanxData $ebanxHelper)
    {
        $this->ebanxHelper = $ebanxHelper;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);

        $order = $paymentDataObject->getOrder();
        $payment = $paymentDataObject->getPayment();
        $fullOrder = $payment->getOrder();

        $currencyCode = $fullOrder->getOrderCurrencyCode();
        $amount = $fullOrder->getGrandTotal();


        return [
            "amount" => $amount,
            "currencyCode" => $currencyCode,
            "orderIncrementId" => $order->getOrderIncrementId()
        ];
    }
}