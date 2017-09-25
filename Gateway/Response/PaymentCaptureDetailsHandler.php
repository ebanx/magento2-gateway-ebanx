<?php
namespace Ebanx\Payments\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

class PaymentCaptureDetailsHandler implements HandlerInterface
{
    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = SubjectReader::readPayment($handlingSubject);

        /** @var OrderPaymentInterface $payment */
        $payment = $payment->getPayment();

        // set token as lastTransId only!
        $payment->setLastTransId($response['token']);

        /**
         * close current transaction because you have capture the goods
         * but do not close the authorisation becasue you can still cancel/refund order
         */
        $payment->setIsTransactionClosed(true);
        $payment->setShouldCloseParentTransaction(false);
    }
}