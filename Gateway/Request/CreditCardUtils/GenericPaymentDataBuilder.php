<?php


namespace DigitalHub\Ebanx\Gateway\Request\CreditCardUtils;

use Magento\Payment\Gateway\Request\BuilderInterface;

use DigitalHub\Ebanx\Observer\GenericObserver\GenericDataAssignObserver;


class GenericPaymentDataBuilder implements BuilderInterface
{
	private $_ebanxHelper;
	private $_logger;
	private $_session;

	private $appState;

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

	public function build(array $buildSubject)
	{
		$paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);
		$payment = $paymentDataObject->getPayment();
		$order = $paymentDataObject->getOrder();

		$additionalData = $payment->getAdditionalInformation();

		$this->_logger->info('PaymentDataBuilder :: build');

		$this->_logger->info('Customer ID', [$this->_session->getQuote()->getCustomerId()]);

		if(isset($additionalData[GenericDataAssignObserver::USE_SAVED_CC]) && $additionalData[GenericDataAssignObserver::USE_SAVED_CC] && $this->_session->getQuote()->getCustomerId()){

			$tokenObject = $this->tokenModel->getTokenByIdAndCustomer($additionalData[GenericDataAssignObserver::USE_SAVED_CC], (int)$this->_session->getQuote()->getCustomerId());

			if(!$tokenObject){
				throw new \Exception('The requested saved credit card not exists');
			}

			$card = new \Ebanx\Benjamin\Models\Card([
				'autoCapture' => (bool)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'capture'),
				'token' => $tokenObject->getToken(),
				'type' => $tokenObject->getPaymentTypeCode()
			]);
		} else {
			$card = new \Ebanx\Benjamin\Models\Card([
				'autoCapture' => (bool)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'capture'),
				'token' => $additionalData[GenericDataAssignObserver::TOKEN],
				'cvv' => $additionalData[GenericDataAssignObserver::CVV],
				'type' => $additionalData[GenericDataAssignObserver::PAYMENT_TYPE_CODE],
			]);
		}

		$request = [
			'type' => 'creditcard',
			'instalments' => $additionalData[GenericDataAssignObserver::INSTALLMENTS],
			'card' => $card,
			'amountTotal' => $additionalData[GenericDataAssignObserver::INSTALLMENTS] > 1 ? $this->_ebanxHelper->calculateTotalWithInterest($order->getGrandTotalAmount(), $additionalData[GenericDataAssignObserver::INSTALLMENTS]) : $order->getGrandTotalAmount(),
		];

		$this->_logger->info('PaymentDataBuilder :: build', $request);

		return $request;
	}
}
