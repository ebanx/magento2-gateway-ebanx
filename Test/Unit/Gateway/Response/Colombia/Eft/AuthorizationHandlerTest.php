<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Response\Colombia\Eft;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;

use DigitalHub\Ebanx\Gateway\Response\Colombia\Eft\AuthorizationHandler;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Logger\Logger;

class AuthorizationHandlerTest extends \PHPUnit\Framework\TestCase
{
    public function testHandle()
    {
        $response = [
            'payment_result' => [
                'payment' => [
                    'hash' => '04723094730470457045759475'
                ]
            ]
        ];

        $orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCanSendNewEmailFlag'])
            ->getMock();

        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderMock->expects($this->once())
            ->method('setCanSendNewEmailFlag')
            ->with(false);

        $paymentDOMock->expects(static::once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentModelMock->expects(static::once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $paymentModelMock->expects(static::once())
            ->method('setTransactionId')
            ->with($response['payment_result']['payment']['hash']);

        $paymentModelMock->expects(static::once())
            ->method('setAdditionalInformation')
            ->with('transaction_data', (array)$response['payment_result']);

        $paymentModelMock->expects(static::once())
            ->method('setIsTransactionClosed')
            ->with(false);

        $ebanxHelperMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $request = new AuthorizationHandler($ebanxHelperMock, $loggerMock);
        $request->handle(['payment' => $paymentDOMock], $response);
    }
}
