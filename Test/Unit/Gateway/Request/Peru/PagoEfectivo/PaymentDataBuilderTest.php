<?php
namespace DigitalHub\Ebanx\Test\Gateway\Request\Peru\PagoEfectivo;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Framework\Model\Context;

use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Gateway\Request\Peru\PagoEfectivo\PaymentDataBuilder;

class PaymentDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuild()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $storeId = 1;
        $days = 2;
        $type = 'pagoefectivo';
        $dueDate = new \DateTime(date('Y-m-d', strtotime('now +' . $days . 'days')) . ' 00:00:00');
        $amountTotal = 123.45;

        $expectation = [
            'type' => $type,
            'dueDate' => $dueDate,
            'amountTotal' => $amountTotal
        ];

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

        $ebanxHelperMock->expects($this->once())
            ->method('getConfigData')
            ->with('digitalhub_ebanx_global/cash', 'cash_expiration_days')
            ->willReturn(2);

        $request = new PaymentDataBuilder($ebanxHelperMock, $contextMock, $loggerMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }
}
