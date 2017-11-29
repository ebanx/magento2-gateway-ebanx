<?php
namespace Ebanx\Payments\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order;

class PaymentCommentHistoryHandler implements HandlerInterface
{

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return $this
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($handlingSubject);

        /** @var Order $order */
        $order = $paymentDataObject->getPayment()->getOrder();

        $hash = "";
        if (isset($response['hash'])) {
            $hash = $response['hash'];
        }

        $message = 'EBANX hash: ' . $hash;
        $order->addStatusHistoryComment($message);

        return $this;
    }
}
