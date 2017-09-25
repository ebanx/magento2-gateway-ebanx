<?php
namespace Ebanx\Payments\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;

class PaymentAuthorisationDetailsHandler implements HandlerInterface
{
    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);

        /** @var OrderPaymentInterface $payment */
        $payment = $payment->getPayment();

        // set transaction not to processing by default wait for notification
        $payment->setIsTransactionPending(true);

        // no not send order confirmation mail
        $payment->getOrder()->setCanSendNewEmailFlag(false);

        // TODO: set token as transactionId
        $payment->setCcTransId($response['token']);
        $payment->setLastTransId($response['token']);

        // TODO: set transaction id
        $payment->setTransactionId($response['token']);

        // do not close transaction so you can do a cancel() and void
        $payment->setIsTransactionClosed(false);
        $payment->setShouldCloseParentTransaction(false);

    }
}