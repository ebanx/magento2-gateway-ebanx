<?php
namespace DigitalHub\Ebanx\Test\Gateway\Request\Mexico\Spei;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Checkout\Model\Session;
use Magento\Framework\Model\Context;

use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Gateway\Request\Mexico\Spei\PaymentDataBuilder;

class PaymentDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuild()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $storeId = 1;
        $type = 'spei';
        $amountTotal = 123.45;

        $expectation = [
            'type' => $type,
            'amountTotal' => $amountTotal
        ];

        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderMock = $this->getMockBuilder(OrderAdapterInterface::class)->getMock();
        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
        $ebanxHelperMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $paymentModelMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->willReturn(null);

        $paymentDOMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentDOMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);

        $orderMock->expects($this->once())
            ->method('getGrandTotalAmount')
            ->willReturn($amountTotal);

        $orderMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        $request = new PaymentDataBuilder($ebanxHelperMock, $contextMock, $loggerMock, $sessionMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }
}
