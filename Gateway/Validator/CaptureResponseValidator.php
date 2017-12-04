<?php
namespace Ebanx\Payments\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Ebanx\Payments\Logger\EbanxLogger;
use Magento\Payment\Gateway\Helper\SubjectReader;

class CaptureResponseValidator extends AbstractValidator
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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);

        $isValid = true;
        $errorMessages = [];

//        TODO: Validate response: Capture
//        if ($response['response'] != '[capture-received]') {
//            $errorMsg = __('Error with capture');
//            $this->ebanxLogger->error($errorMsg);
//            $errorMessages[] = $errorMsg;
//        }

        return $this->createResult($isValid, $errorMessages);
    }
}
