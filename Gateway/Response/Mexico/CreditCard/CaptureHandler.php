<?php
namespace DigitalHub\Ebanx\Gateway\Response\Mexico\CreditCard;

use Magento\Payment\Gateway\Response\HandlerInterface;

class CaptureHandler implements HandlerInterface
{
    protected $_logger;
    protected $ebanxHelper;

    public function __construct(
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;

        $this->_logger->info('CaptureHandler :: __construct');
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);
        $payment = $payment->getPayment();

        $this->_logger->info('CaptureHandler :: handle');

        $payment->setIsTransactionPending(false);
        $payment->setIsTransactionClosed(true);
        $payment->setShouldCloseParentTransaction(true);
    }
}
