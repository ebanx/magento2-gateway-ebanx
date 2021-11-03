<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Colombia\CreditCard;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use DigitalHub\Ebanx\Observer\Colombia\CreditCard\DataAssignObserver;

class AuthorizationValidator extends AbstractValidator
{
    protected $_ebanxHelper;
    protected $_logger;
    protected $_eventManager;
    protected $_session;

    /**
    * AuthorizationValidator constructor.
    *
    * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
    * @param \DigitalHub\Ebanx\Helper\Data $_ebanxHelper
    * @param \DigitalHub\Ebanx\Logger\Logger $logger
    * @param \Magento\Framework\Event\Manager $eventManager
    * @param \Magento\Checkout\Model\Session $session
    */
    public function __construct(
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory,
        \DigitalHub\Ebanx\Helper\Data $_ebanxHelper,
        \DigitalHub\Ebanx\Logger\Logger $logger,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Checkout\Model\Session $session
    )
    {
        $this->_ebanxHelper = $_ebanxHelper;
        $this->_logger = $logger;
        $this->_eventManager = $eventManager;
        $this->_session = $session;

        $this->_logger->info('AuthorizationValidator :: __construct');

        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $response = \Magento\Payment\Gateway\Helper\SubjectReader::readResponse($validationSubject);
        $paymentDataObjectInterface = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($validationSubject);
        $payment = $paymentDataObjectInterface->getPayment();
        $additionalData = $payment->getAdditionalInformation();

        $this->_logger->info('AuthorizationValidator :: handle');

        $errorMessages = [];
        $isValid = true;

        try {
            $transactionResult = $response['payment_result'];
            if($transactionResult['status'] == 'SUCCESS'){
                if($transactionResult['payment']['transaction_status']['code'] != 'OK'){
                    $errorDescription = $transactionResult['payment']['transaction_status']['description'];
                    $errorMessage = $this->_ebanxHelper->filterErrorMessageForCountry($errorDescription, 'CO');
                    throw new \Exception($errorMessage);
                }

                // Save credit card token
                if(isset($additionalData[DataAssignObserver::SAVE_CC])){
                    if((bool)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'save')){
                        // If has logged user and the option "Save credit card data" is set
                        if($payment->getOrder()->getCustomerId() && (bool)$additionalData[DataAssignObserver::SAVE_CC]) {
                            $token_data = [
                                'token' => $additionalData[DataAssignObserver::TOKEN],
                                'payment_type_code' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
                                'masked_card_number' => $additionalData[DataAssignObserver::MASKED_CARD_NUMBER],
                                'customer_id' => $payment->getOrder()->getCustomerId()
                            ];
                            $data = new \Magento\Framework\DataObject($token_data);
                            $this->_eventManager->dispatch('digitalhub_ebanx_assign_colombia_creditcard_token', ['token_data' => $data]);
                        }
                    }
                }
            } else {
                throw new \Exception($response['payment_result']['status_message']);
            }
        } catch (\Exception $e){
            $isValid = false;
            $errorMessages[] = $e->getMessage();

            $this->_logger->info(sprintf('EBANX Exception `%s` :: `%s`', get_class($e), __METHOD__), [$e->getMessage()]);
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
