<?php
namespace DigitalHub\Ebanx\Gateway\Response\Chile\Sencillito;

use Magento\Payment\Gateway\Response\HandlerInterface;

class AuthorizationHandler implements HandlerInterface
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

        $this->_logger->info('AuthorizationHandler :: __construct');
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);
        $payment = $payment->getPayment();

        // $this->_logger->info('AuthorizationHandler :: handle');

        $payment_result_data = (array)$response['payment_result'];
        //
        // $this->_logger->info('AuthorizationHandler :: payment result data', $payment_result_data);
        // $this->_logger->info('AuthorizationHandler :: payment result hash', [$payment_result_data['payment']['hash']]);

        $payment->setTransactionId($payment_result_data['payment']['hash']);
        $payment->setAdditionalInformation('transaction_data', $payment_result_data);

        // set transaction not to processing by default wait for notification
        $payment->setIsTransactionPending(true);

        // no not send order confirmation mail
        $payment->getOrder()->setCanSendNewEmailFlag(false);

        // do not close transaction so you can do a cancel() and void
        $payment->setIsTransactionClosed(false);
        $payment->setShouldCloseParentTransaction(false);
    }
}
