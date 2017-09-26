<?php
namespace Ebanx\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class CustomerDataBuilder
 */
class CancelDataBuilder implements BuilderInterface
{

    /**
     * @var \Ebanx\Payments\Helper\Data
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
     * Create cancel_or_refund request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $order = $paymentDataObject->getOrder();
        $payment = $paymentDataObject->getPayment();
        $token = $payment->getCcTransId();

        return [
            "incrementalId" => $order->getOrderIncrementId(),
            "token" => $token
        ];
    }
}