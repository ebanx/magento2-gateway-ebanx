<?php
namespace DigitalHub\Ebanx\Test\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;

use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Gateway\Request\AddressDataBuilder;

class AddressDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuild()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $expectation = [
            'address' => new \Ebanx\Benjamin\Models\Address([
                'address' => 'Rua teste',
                'streetNumber' => '123',
                'city' => 'Curitiba',
                'country' => \Ebanx\Benjamin\Models\Country::fromIso('BR'),
                'state' => 'PR',
                'streetComplement' => '',
                'zipcode' => '80010-010'
            ])
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

        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelperMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fakeBillingAddress = new \Magento\Framework\DataObject();
        $fakeBillingAddress->setData([
            'city' => 'Curitiba',
            'region_code' => 'PR',
            'country_id' => 'BR',
            'postcode' => '80010-010'
        ]);

        $quoteMock->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($fakeBillingAddress);

        $ebanxHelperMock->expects($this->any())
            ->method('getAddressData')
            ->withConsecutive(
                ['street', $fakeBillingAddress],
                ['street_number', $fakeBillingAddress],
                ['complement', $fakeBillingAddress]
            )
            ->willReturnOnConsecutiveCalls('Rua teste', '123', '');

        $sessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        // Test build
        $request = new AddressDataBuilder($sessionMock, $ebanxHelperMock, $loggerMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }
}
