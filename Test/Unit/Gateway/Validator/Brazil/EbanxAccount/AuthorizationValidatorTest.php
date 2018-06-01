<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Validator\Brazil\EbanxAccount;

use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;
use \Magento\Checkout\Model\Session;

use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Gateway\Validator\Brazil\EbanxAccount\AuthorizationValidator;

class AuthorizationValidatorTest extends \PHPUnit\Framework\TestCase
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

        $session = $this->getMockBuilder(Session::class)
            ->setMethods(['setEbanxRedirectUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $session->expects($this->once())
            ->method('setEbanxRedirectUrl')
            ->with('http://teste.ebanx.com');

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $validationSubject = [
            'response' => [
                'payment_result' => [
                    'status' => 'SUCCESS',
                    'redirect_url' => 'http://teste.ebanx.com'
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

        $validator = new AuthorizationValidator($resultFactory, $ebanxHelper, $logger, $session);
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

        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $validationSubject = [
            'response' => [
                'payment_result' => [
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

        $validator = new AuthorizationValidator($resultFactory, $ebanxHelper, $logger, $session);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }
}
