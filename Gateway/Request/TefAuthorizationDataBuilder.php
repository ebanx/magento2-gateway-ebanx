<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Payments\Observer\TefDataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class TefAuthorizationDataBuilder implements BuilderInterface
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
        $selectedBank = $payment->getAdditionalInformation(TefDataAssignObserver::SELECTED_BANK);

        return [
            'bankCode' => $selectedBank,
        ];
    }
}
