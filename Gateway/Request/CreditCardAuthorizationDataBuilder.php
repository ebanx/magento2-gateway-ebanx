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

        $rates = json_decode($this->ebanxHelper->getEbanxAbstractConfigData('interest_rates'), true);

        usort($rates, function ($value, $previous) {
            if ($value['instalments'] === $previous['instalments']) {
                return 0;
            }

            return ($value['instalments'] < $previous['instalments']) ? -1 : 1;
        });

        var_dump($rates);

        $card = new Card([
            'autoCapture' => $this->shouldAutoCapture($storeId),
            'token' => $payment->getAdditionalInformation(CreditCardDataAssignObserver::TOKEN),
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
