<?php
namespace DigitalHub\Ebanx\Gateway\Response\Brazil\Boleto;

use Magento\Payment\Gateway\Response\HandlerInterface;

class CancelHandler implements HandlerInterface
{
    protected $_logger;

    public function __construct(
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_logger = $logger;

        $this->_logger->info('CancelHandler :: __construct');
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);
        $payment = $payment->getPayment();

        $this->_logger->info('CancelHandler :: handle');

        $payment->setIsTransactionPending(false);
        $payment->setIsTransactionClosed(true);
        $payment->setShouldCloseParentTransaction(true);
    }
}
