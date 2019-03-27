<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Argentina\CreditCard;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class CancelValidator extends AbstractValidator
{
    protected $_logger;
    protected $ebanxHelper;

    /**
    * CancelValidator constructor.
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

        $this->_logger->info('CancelValidator :: __construct');

        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $response = \Magento\Payment\Gateway\Helper\SubjectReader::readResponse($validationSubject);

        $this->_logger->info('CancelValidator :: handle');

        $this->_logger->info('CancelValidator: response', [$response['cancel_result']]);

        $errorMessages = [];
        $isValid = true;

        try {
            $transactionResult = $response['cancel_result'];
            if($transactionResult['status'] != 'SUCCESS'){
                throw new \Exception($response['cancel_result']['status_message']);
            }
        } catch (\Exception $e){
            $isValid = false;
            $errorMessages[] = $e->getMessage();
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
