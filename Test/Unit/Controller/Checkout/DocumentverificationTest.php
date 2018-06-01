<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\Checkout;

class DocumentverificationTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->once())
            ->method('getCustomerDocumentNumberField')
            ->with($quoteMock)
            ->willReturn('taxvat');

        $ebanxHelper->expects($this->once())
            ->method('getCustomerDocumentNumber')
            ->with($quoteMock, 'taxvat')
            ->willReturn('1234567890');

        $session->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $jsonMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'has_document_number' => true
            ])
            ->willReturn($jsonMock);

        $resultFactory->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $controller = new \DigitalHub\Ebanx\Controller\Checkout\Documentverification($context, $resultFactory, $ebanxHelper, $session, $logger);

        $this->assertSame($jsonMock, $controller->execute());
    }

    public function testExecuteHasNoDocument()
    {

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->once())
            ->method('getCustomerDocumentNumberField')
            ->with($quoteMock)
            ->willReturn('taxvat');

        $ebanxHelper->expects($this->once())
            ->method('getCustomerDocumentNumber')
            ->with($quoteMock, 'taxvat')
            ->willReturn('');

        $session->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $jsonMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $jsonMock->expects($this->once())
            ->method('setData')
            ->with([
                'has_document_number' => false
            ])
            ->willReturn($jsonMock);

        $resultFactory->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $controller = new \DigitalHub\Ebanx\Controller\Checkout\Documentverification($context, $resultFactory, $ebanxHelper, $session, $logger);

        $this->assertSame($jsonMock, $controller->execute());
    }
}
