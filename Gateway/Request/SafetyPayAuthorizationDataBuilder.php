<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Payments\Observer\SafetyPayDataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class SafetyPayAuthorizationDataBuilder implements BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return mixed
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();
        $safetyPayType = $payment->getAdditionalInformation(SafetyPayDataAssignObserver::SAFETYPAY_TYPE);

        return [
            'type' => 'tef',
            'safetyPayType' => $safetyPayType,
        ];
    }
}
