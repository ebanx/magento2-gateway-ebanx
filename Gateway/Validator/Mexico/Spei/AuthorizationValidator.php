<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Mexico\Spei;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class AuthorizationValidator extends AbstractValidator
{
    protected $_ebanxHelper;
    protected $_logger;

    /**
    * AuthorizationValidator constructor.
    *
    * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
    * @param \DigitalHub\Ebanx\Helper\Data $_ebanxHelper
    * @param \DigitalHub\Ebanx\Logger\Logger $logger
    */
    public function __construct(
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory,
        \DigitalHub\Ebanx\Helper\Data $_ebanxHelper,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_ebanxHelper = $_ebanxHelper;
        $this->_logger = $logger;

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

        $this->_logger->info('AuthorizationValidator :: handle');

        $errorMessages = [];
        $isValid = true;

        try {
            $paymentResult = $response['payment_result'];
            if($paymentResult['status'] != 'SUCCESS'){
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
