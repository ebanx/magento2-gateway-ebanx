<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Colombia\CreditCard;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class RefundValidator extends AbstractValidator
{
    protected $_logger;
    protected $ebanxHelper;

    /**
    * RefundValidator constructor.
    *
    * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
    * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
    * @param \DigitalHub\Ebanx\Logger\Logger $logger
    */
    public function __construct(
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;

        $this->_logger->info('RefundValidator :: __construct');

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

        $this->_logger->info('RefundValidator :: handle');

        $this->_logger->info('RefundValidator: response', [$response['refund_result']]);

        $errorMessages = [];
        $isValid = true;

        try {
            $transactionResult = $response['refund_result'];
            if($transactionResult['status'] != 'SUCCESS'){
                throw new \Exception($response['refund_result']['status_message']);
            }
        } catch (\Exception $e){
            $isValid = false;
            $errorMessages[] = $e->getMessage();
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
