<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Response\Argentina\CreditCard;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

use DigitalHub\Ebanx\Gateway\Response\Argentina\CreditCard\RefundHandler;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Logger\Logger;

class RefundHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testHandle()
    {
        $response = [];

        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentModelMock->expects($this->once())
            ->method('setIsTransactionPending')
            ->with(false);

        $paymentModelMock->expects($this->once())
            ->method('setIsTransactionClosed')
            ->with(true);

        $paymentModelMock->expects($this->once())
            ->method('setShouldCloseParentTransaction')
            ->with(true);

        $ebanxHelperMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $request = new RefundHandler($ebanxHelperMock, $loggerMock);
        $request->handle(['payment' => $paymentDOMock], $response);
    }
}
