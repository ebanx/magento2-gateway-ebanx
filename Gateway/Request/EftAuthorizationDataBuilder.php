<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Payments\Observer\EftDataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class EftAuthorizationDataBuilder implements BuilderInterface
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
        $eftSelectedBank = $payment->getAdditionalInformation(EftDataAssignObserver::EFT_SELECTED_BANK);

        return [
            'type' => 'eft',
            'bankCode' => $eftSelectedBank,
        ];
    }
}
