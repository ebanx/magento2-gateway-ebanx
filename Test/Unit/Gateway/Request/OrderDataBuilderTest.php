<?php
namespace DigitalHub\Ebanx\Test\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Quote\Model\Quote;

use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Gateway\Request\OrderDataBuilder;

class OrderDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuild()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $expectation = [
            'orderNumber' => '000000123',
            'merchantPaymentCode' => '000000123' . '_' . time(),
            'items' => [
                new \Ebanx\Benjamin\Models\Item([
                    'sku' => 'ABC123',
                    'name' => 'Produto Exemplo 123',
                    'unitPrice' => 129.99,
                    'quantity' => 1
                ])
            ],
            'userValues' => array(
               1 => 'from_magento2',
               3 => '1.0.8',
           )
        ];

        // Mocks
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderMock = $this->getMockBuilder(OrderAdapterInterface::class)
            ->getMock();
        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)
            ->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->setMethods(['getAdditionalInformation'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock Methods
        $paymentModelMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->willReturn([]);

        $paymentDOMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentDOMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);

        $fakeOrderItem = new \Magento\Framework\DataObject();
        $fakeOrderItem->setData([
            'sku' => 'ABC123',
            'name' => 'Produto Exemplo 123',
            'price' => 129.99,
            'qty_ordered' => 1
        ]);

        $orderMock->expects($this->any())
            ->method('getItems')
            ->willReturn([
                $fakeOrderItem
            ]);
        $orderMock->expects($this->any())
            ->method('getOrderIncrementId')
            ->willReturn('000000123');

        $ebanxHelperMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fakeBillingAddress = new \Magento\Framework\DataObject();
        $fakeBillingAddress->setData([
            'country_id' => 'BR',
            'email' => 'test@teste123.com',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'telephone' => '11 3333-2222',
        ]);

        // Test build
        $request = new OrderDataBuilder($ebanxHelperMock, $loggerMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }
}
