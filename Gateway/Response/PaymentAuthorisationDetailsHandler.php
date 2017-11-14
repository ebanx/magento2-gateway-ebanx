<?php
namespace Ebanx\Payments\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class PaymentAuthorisationDetailsHandler implements HandlerInterface
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

        $payment->setIsTransactionPending(true);

        $payment->getOrder()->setCanSendNewEmailFlag(false);

        $payment->setCcTransId($response['hash']);
        $payment->setLastTransId($response['hash']);

        $payment->setTransactionId($response['hash']);

        $payment->setIsTransactionClosed(false);
        $payment->setShouldCloseParentTransaction(false);

    }
}
