<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Colombia\CreditCard;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class CaptureValidator extends AbstractValidator
{
    protected $_logger;
    protected $ebanxHelper;

    /**
    * CaptureValidator constructor.
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

        $this->_logger->info('CaptureValidator :: __construct');

        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $response = \Magento\Payment\Gateway\Helper\SubjectReader::readResponse($validationSubject);

        $this->_logger->info('CaptureValidator :: handle');

        $this->_logger->info('CaptureValidator: response', [$response['capture_result']]);

        $errorMessages = [];
        $isValid = true;

        try {
            $transactionResult = $response['capture_result'];
            if($transactionResult['status'] != 'SUCCESS'){
                throw new \Exception($response['capture_result']['status_message']);
            }
        } catch (\Exception $e){
            $isValid = false;
            $errorMessages[] = $e->getMessage();

            $this->_logger->info(sprintf('EBANX Exception `%s` :: `%s`', get_class($e), __METHOD__), [$e->getMessage()]);
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
