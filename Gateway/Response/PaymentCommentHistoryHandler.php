<?php
namespace Ebanx\Payments\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;

class PaymentCommentHistoryHandler implements HandlerInterface
{

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return $this
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($handlingSubject);

        /** @var OrderPaymentInterface $payment */
        $payment = $payment->getPayment();

        if (isset($response['token'])) {
            $token = $response['token'];
        } else {
            $token = "";
        }

        $message = 'EBANX token: ' . $token;
        $payment->getOrder()->addStatusHistoryComment($message);

        return $this;
    }
}
