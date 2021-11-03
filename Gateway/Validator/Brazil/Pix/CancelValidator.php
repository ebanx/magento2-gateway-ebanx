<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Brazil\Pix;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class CancelValidator extends AbstractValidator
{
    protected $_logger;

    /**
    * CancelValidator constructor.
    *
    * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
    * @param \DigitalHub\Ebanx\Logger\Logger $logger
    */
    public function __construct(
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
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
        $this->_logger->info('CancelValidator :: handle');

        $this->_logger->info(sprintf('EBANX Exception `%s` :: `%s`', get_class($e), __METHOD__), [$e->getMessage()]);

        return $this->createResult(true, []);
    }
}
