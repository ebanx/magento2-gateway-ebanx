<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Card;
use Ebanx\Payments\Helper\Data as EbanxData;
use Ebanx\Payments\Observer\CreditCardDataAssignObserver;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CreditCardAuthorizationDataBuilder implements BuilderInterface
{
    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;
    private $currency;

    /**
     * CaptureDataBuilder constructor.
     *
     * @param EbanxData $ebanxHelper
     */
    public function __construct(EbanxData $ebanxHelper)
    {
        $this->ebanxHelper = $ebanxHelper;
    }

    /**
     * @param array $buildSubject
     * @return mixed
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $order = $paymentDataObject->getOrder();
        $payment = $paymentDataObject->getPayment();
        $storeId = $order->getStoreId();

        $card = new Card([
            'autoCapture' => $this->shouldAutoCapture($storeId),
            'token' => '7c424206e7a5a5d4bb2380ef72dd6bbad830108f770a45a8255ae7c8b579282914941893fe6059a69d680d2f178a06e6fcfdc2bdd0de0d5cc3c23cc5a5dcec74',
            'cvv' => $payment->getAdditionalInformation(CreditCardDataAssignObserver::CVV),
            'type' => $payment->getAdditionalInformation(CreditCardDataAssignObserver::BRAND),
        ]);

        return [
            'type' => 'creditcard',
            'instalments' => $payment->getAdditionalInformation(CreditCardDataAssignObserver::INSTALMENTS),
            'card' => $card,
            'token' => $card->token,
        ];
    }

    /**
     * @param $storeId
     *
     * @return bool
     */
    private function shouldAutoCapture($storeId)
    {
        return $this->ebanxHelper->getEbanxAbstractConfigData('auto_capture', $storeId) === '1';
    }
}
