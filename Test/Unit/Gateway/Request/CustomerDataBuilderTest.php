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
use DigitalHub\Ebanx\Gateway\Request\CustomerDataBuilder;

class CustomerDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testBuild()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $documentNumber = '1234567890';

        $person = new \Ebanx\Benjamin\Models\Person([
            'type' => \Ebanx\Benjamin\Models\Person::TYPE_PERSONAL,
            'document' => $documentNumber,
            'email' => 'test@teste123.com',
            'name' => 'Firstname Lastname',
            'phoneNumber' => '11 3333-2222',
            'ip' => '127.0.0.1',
        ]);

        $expectation = [
            'person' => $person,
            'responsible' => $person,
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
            ->willReturn([
                'document_number' => $documentNumber
            ]);

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
            'country_id' => 'BR',
            'email' => 'test@teste123.com',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'telephone' => '11 3333-2222',
        ]);

        $quoteMock->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($fakeBillingAddress);

        $orderMock->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($fakeBillingAddress);

        $orderMock->expects($this->any())
            ->method('getRemoteIp')
            ->willReturn('127.0.0.1');

        $ebanxHelperMock->expects($this->any())
            ->method('getAddressData')
            ->withConsecutive(
                ['street', $fakeBillingAddress],
                ['street_number', $fakeBillingAddress],
                ['complement', $fakeBillingAddress]
            )
            ->willReturnOnConsecutiveCalls('Rua teste', '123', '');

        $ebanxHelperMock->expects($this->any())
            ->method('getCustomerDocumentNumberField')
            ->with($quoteMock)
            ->willReturn('taxvat');

        $ebanxHelperMock->expects($this->any())
            ->method('getPersonType')
            ->with($documentNumber, $fakeBillingAddress->getCountryId())
            ->willReturn(\Ebanx\Benjamin\Models\Person::TYPE_PERSONAL);

        $ebanxHelperMock->expects($this->any())
            ->method('getCustomerDocumentNumber')
            ->with($quoteMock, 'taxvat')
            ->willReturn($documentNumber);

        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        // Test build
        $request = new CustomerDataBuilder($ebanxHelperMock, $sessionMock, $loggerMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }

    public function testBuildWithoutDocumentNumber()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $documentNumber = '';

        $person = new \Ebanx\Benjamin\Models\Person([
            'type' => \Ebanx\Benjamin\Models\Person::TYPE_PERSONAL,
            'document' => $documentNumber,
            'email' => 'test@teste123.com',
            'name' => 'Firstname Lastname',
            'phoneNumber' => '11 3333-2222',
            'ip' => '127.0.0.1',
        ]);

        $expectation = [
            'person' => $person,
            'responsible' => $person,
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
            ->willReturn([
                'document_number' => $documentNumber
            ]);

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
            'country_id' => 'BR',
            'email' => 'test@teste123.com',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'telephone' => '11 3333-2222',
        ]);

        $quoteMock->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($fakeBillingAddress);

        $orderMock->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($fakeBillingAddress);

        $orderMock->expects($this->any())
            ->method('getRemoteIp')
            ->willReturn('127.0.0.1');

        $ebanxHelperMock->expects($this->any())
            ->method('getAddressData')
            ->withConsecutive(
                ['street', $fakeBillingAddress],
                ['street_number', $fakeBillingAddress],
                ['complement', $fakeBillingAddress]
            )
            ->willReturnOnConsecutiveCalls('Rua teste', '123', '');

        $ebanxHelperMock->expects($this->any())
            ->method('getCustomerDocumentNumberField')
            ->with($quoteMock)
            ->willReturn('taxvat');

        $ebanxHelperMock->expects($this->any())
            ->method('getPersonType')
            ->with($documentNumber, $fakeBillingAddress->getCountryId())
            ->willReturn(\Ebanx\Benjamin\Models\Person::TYPE_PERSONAL);

        $ebanxHelperMock->expects($this->any())
            ->method('getCustomerDocumentNumber')
            ->with($quoteMock, 'taxvat')
            ->willReturn($documentNumber);

        $sessionMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        // Test build
        $request = new CustomerDataBuilder($ebanxHelperMock, $sessionMock, $loggerMock);

        $this->assertEquals(
            $expectation,
            $request->build(['payment' => $paymentDOMock]) /* $buildSubject */
        );
    }
}
