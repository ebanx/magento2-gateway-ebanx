<?php
namespace Ebanx\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class CustomerDataBuilder
 */
class CaptureDataBuilder implements BuilderInterface
{

    /**
     * @var EbanxData
     */
    private $ebanxHelper;

    /**
     * CaptureDataBuilder constructor.
     *
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     */
    public function __construct(EbanxData $ebanxHelper)
    {
        $this->ebanxHelper = $ebanxHelper;
    }

    /**
     * Create capture request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {

        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $amount =  SubjectReader::readAmount($buildSubject);

        $payment = $paymentDataObject->getPayment();
        $token = $payment->getCcTransId();
        $currency = $payment->getOrder()->getOrderCurrencyCode();

        //format the amount to minor units
        $amount = number_format($amount, 2, '', '');

        $modificationAmount = ['currency' => $currency, 'value' => $amount];

        return [
            "modificationAmount" => $modificationAmount,
            "incrementalId" => $payment->getOrder()->getIncrementId(),
            "token" => $token
        ];
    }
}