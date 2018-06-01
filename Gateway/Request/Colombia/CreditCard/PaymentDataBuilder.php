<?php
namespace DigitalHub\Ebanx\Gateway\Request\Colombia\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
use DigitalHub\Ebanx\Observer\Colombia\CreditCard\DataAssignObserver;

class PaymentDataBuilder implements BuilderInterface
{
    private $_ebanxHelper;
    private $_logger;
    private $_session;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * PaymentDataBuilder constructor.
     *
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \DigitalHub\Ebanx\Logger\Logger $logger
     * @param \Magento\Checkout\Model\Session $session
     * @param \DigitalHub\Ebanx\Model\CreditCard\Token $tokenModel
     */
    public function __construct(
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Framework\Model\Context $context,
        \DigitalHub\Ebanx\Logger\Logger $logger,
        \Magento\Checkout\Model\Session $session,
        \DigitalHub\Ebanx\Model\CreditCard\Token $tokenModel
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;
        $this->_session = $session;
        $this->tokenModel = $tokenModel;
        $this->appState = $context->getAppState();

        $this->_logger->info('PaymentDataBuilder :: __construct');
    }

    /**
     * @param array $buildSubject
     * @return mixed
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();
        $order = $paymentDataObject->getOrder();
        $storeId = $order->getStoreId();

        // $this->_logger->info('Request::build order', [$order->getOrderIncrementId()]);
        // $this->_logger->info('Request::build payment', $payment->getData());

        $additionalData = $payment->getAdditionalInformation();

        $this->_logger->info('PaymentDataBuilder :: build');

        if(isset($additionalData[DataAssignObserver::USE_SAVED_CC]) && $additionalData[DataAssignObserver::USE_SAVED_CC] && $this->_session->getQuote()->getCustomerId()){

            $tokenObject = $this->tokenModel->getTokenByIdAndCustomer($additionalData[DataAssignObserver::USE_SAVED_CC], (int)$this->_session->getQuote()->getCustomerId());

            if(!$tokenObject){
                throw new \Exception('The requested saved credit card not exists');
            }

            $card = new \Ebanx\Benjamin\Models\Card([
                'token' => $tokenObject->getToken(),
                'type' => $tokenObject->getPaymentTypeCode()
            ]);
        } else {
            $card = new \Ebanx\Benjamin\Models\Card([
                'token' => $additionalData[DataAssignObserver::TOKEN],
                'cvv' => $additionalData[DataAssignObserver::CVV],
                'type' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
            ]);
        }

        $request = [
            'type' => 'creditcard',
            'instalments' => $additionalData[DataAssignObserver::INSTALLMENTS],
            'card' => $card,
            'amountTotal' => $additionalData[DataAssignObserver::INSTALLMENTS] > 1 ? $this->_ebanxHelper->calculateTotalWithInterest($order->getGrandTotalAmount(), $additionalData[DataAssignObserver::INSTALLMENTS]) : $order->getGrandTotalAmount(),
        ];

        $this->_logger->info('PaymentDataBuilder :: build', $request);

        return $request;
    }
}
