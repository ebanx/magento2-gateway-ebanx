<?php
namespace Ebanx\Payments\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Ebanx\Payments\Logger\EbanxLogger;

class CancelResponseValidator extends AbstractValidator
{

    /**
     * @var \Ebanx\Payments\Logger\EbanxLogger
     */
    private $ebanxLogger;

    /**
     * GeneralResponseValidator constructor.
     *
     * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
     * @param \Ebanx\Payments\Logger\EbanxLogger $ebanxLogger
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        EbanxLogger $ebanxLogger
    ) {
        $this->ebanxLogger = $ebanxLogger;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);

        $isValid = true;
        $errorMessages = [];

//        TODO: Validate response: Cancel
//        if ($response['response'] != '[cancelOrRefund-received]') {
//            $errorMsg = __('Error with cancellation');
//            $this->ebanxLogger->error($errorMsg);
//            $errorMessages[] = $errorMsg;
//        }

        return $this->createResult($isValid, $errorMessages);
    }
}