<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Validator\Mexico\CreditCard;

use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Gateway\Validator\Mexico\CreditCard\CancelValidator;

class CancelValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testSuccess()
    {
        $expectation = [];

        $resultMock = $this->getMockBuilder(ResultInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactory = $this->getMockBuilder(\Magento\Payment\Gateway\Validator\ResultInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)
            ->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $validationSubject = [
            'response' => [
                'cancel_result' => [
                    'status' => 'SUCCESS'
                ]
            ],
            'payment' => $paymentDOMock
        ];

        $resultFactory->expects($this->once())
            ->method('create')
            ->with([
                'isValid' => true,
                'failsDescription' => []
            ])
            ->willReturn($resultMock);

        $validator = new CancelValidator($resultFactory, $ebanxHelper, $logger);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }

    public function testError()
    {
        $expectation = [];

        $resultMock = $this->getMockBuilder(ResultInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactory = $this->getMockBuilder(\Magento\Payment\Gateway\Validator\ResultInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock = $this->getMockBuilder(PaymentDataObjectInterface::class)
            ->getMock();
        $paymentModelMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $validationSubject = [
            'response' => [
                'cancel_result' => [
                    'status' => 'ERROR',
                    'status_message' => 'Error message'
                ]
            ],
            'payment' => $paymentDOMock
        ];

        $resultFactory->expects($this->once())
            ->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => ['Error message']
            ])
            ->willReturn($resultMock);

        $validator = new CancelValidator($resultFactory, $ebanxHelper, $logger);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }
}
