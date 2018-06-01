<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Validator\Mexico\CreditCard;

use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Framework\Event\Manager;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order;

use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Gateway\Validator\Mexico\CreditCard\AuthorizationValidator;
use DigitalHub\Ebanx\Observer\Mexico\CreditCard\DataAssignObserver;

class AuthorizationValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function testSuccessWithSaveCc()
    {
        $expectation = [];

        $customerId = 1;

        $additionalData = [
            DataAssignObserver::SAVE_CC => 1,
            DataAssignObserver::TOKEN => '50346593465946593465934865983465',
            DataAssignObserver::PAYMENT_TYPE_CODE => 'visa',
            DataAssignObserver::MASKED_CARD_NUMBER => '4111xxxxxxxx1111'
        ];

        $eventTokenData = $data = new \Magento\Framework\DataObject([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'payment_type_code' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
            'masked_card_number' => $additionalData[DataAssignObserver::MASKED_CARD_NUMBER],
            'customer_id' => $customerId
        ]);

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
            ->setMethods(['getAdditionalInformation','getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderModelMock = $this->getMockBuilder(Order::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->with('digitalhub_ebanx_global/cc', 'save')
            ->willReturn(1);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventManagerMock = $this->getMockBuilder(Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentModelMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentModelMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderModelMock);

        $orderModelMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('digitalhub_ebanx_assign_mexico_creditcard_token', ['token_data' => $eventTokenData]);

        $validationSubject = [
            'response' => [
                'payment_result' => [
                    'status' => 'SUCCESS',
                    'payment' => [
                        'transaction_status' => [
                            'code' => 'OK'
                        ]
                    ]
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

        $validator = new AuthorizationValidator($resultFactory, $ebanxHelper, $logger, $eventManagerMock, $session);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }

    public function testErrorWithSaveCc()
    {
        $expectation = [];

        $customerId = 1;

        $additionalData = [
            DataAssignObserver::SAVE_CC => 1,
            DataAssignObserver::TOKEN => '50346593465946593465934865983465',
            DataAssignObserver::PAYMENT_TYPE_CODE => 'visa',
            DataAssignObserver::MASKED_CARD_NUMBER => '4111xxxxxxxx1111'
        ];

        $eventTokenData = $data = new \Magento\Framework\DataObject([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'payment_type_code' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
            'masked_card_number' => $additionalData[DataAssignObserver::MASKED_CARD_NUMBER],
            'customer_id' => $customerId
        ]);

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
            ->setMethods(['getAdditionalInformation','getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderModelMock = $this->getMockBuilder(Order::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->with('digitalhub_ebanx_global/cc', 'save')
            ->willReturn(1);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventManagerMock = $this->getMockBuilder(Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentModelMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentModelMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderModelMock);

        $orderModelMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $eventManagerMock->expects($this->never())
            ->method('dispatch')
            ->with($this->anything());

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

        $validator = new AuthorizationValidator($resultFactory, $ebanxHelper, $logger, $eventManagerMock, $session);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }

    public function testTransactionStatusError()
    {
        $expectation = [];

        $customerId = 1;

        $additionalData = [
            DataAssignObserver::SAVE_CC => 1,
            DataAssignObserver::TOKEN => '50346593465946593465934865983465',
            DataAssignObserver::PAYMENT_TYPE_CODE => 'visa',
            DataAssignObserver::MASKED_CARD_NUMBER => '4111xxxxxxxx1111'
        ];

        $eventTokenData = $data = new \Magento\Framework\DataObject([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'payment_type_code' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
            'masked_card_number' => $additionalData[DataAssignObserver::MASKED_CARD_NUMBER],
            'customer_id' => $customerId
        ]);

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
            ->setMethods(['getAdditionalInformation','getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderModelMock = $this->getMockBuilder(Order::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->with('digitalhub_ebanx_global/cc', 'save')
            ->willReturn(1);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventManagerMock = $this->getMockBuilder(Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentModelMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentModelMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderModelMock);

        $orderModelMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $eventManagerMock->expects($this->never())
            ->method('dispatch')
            ->with($this->anything());

        $validationSubject = [
            'response' => [
                'payment_result' => [
                    'status' => 'SUCCESS',
                    'payment' => [
                        'transaction_status' => [
                            'code' => 'ERROR',
                            'description' => 'Transaction Status Error'
                        ]
                    ]
                ]
            ],
            'payment' => $paymentDOMock
        ];

        $resultFactory->expects($this->once())
            ->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => ['Transaction Status Error']
            ])
            ->willReturn($resultMock);

        $validator = new AuthorizationValidator($resultFactory, $ebanxHelper, $logger, $eventManagerMock, $session);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }

    public function testSuccessWithoutSaveCcByUser()
    {
        $expectation = [];

        $customerId = 1;

        $additionalData = [
            DataAssignObserver::SAVE_CC => 0,
            DataAssignObserver::TOKEN => '50346593465946593465934865983465',
            DataAssignObserver::PAYMENT_TYPE_CODE => 'visa',
            DataAssignObserver::MASKED_CARD_NUMBER => '4111xxxxxxxx1111'
        ];

        $eventTokenData = $data = new \Magento\Framework\DataObject([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'payment_type_code' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
            'masked_card_number' => $additionalData[DataAssignObserver::MASKED_CARD_NUMBER],
            'customer_id' => $customerId
        ]);

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
            ->setMethods(['getAdditionalInformation','getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderModelMock = $this->getMockBuilder(Order::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->with('digitalhub_ebanx_global/cc', 'save')
            ->willReturn(1);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventManagerMock = $this->getMockBuilder(Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentModelMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentModelMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderModelMock);

        $orderModelMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $eventManagerMock->expects($this->never())
            ->method('dispatch')
            ->with($this->anything());

        $validationSubject = [
            'response' => [
                'payment_result' => [
                    'status' => 'SUCCESS',
                    'payment' => [
                        'transaction_status' => [
                            'code' => 'OK'
                        ]
                    ]
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

        $validator = new AuthorizationValidator($resultFactory, $ebanxHelper, $logger, $eventManagerMock, $session);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }

    public function testSuccessWithoutSaveCcByConfig()
    {
        $expectation = [];

        $customerId = 1;

        $additionalData = [
            DataAssignObserver::SAVE_CC => 1,
            DataAssignObserver::TOKEN => '50346593465946593465934865983465',
            DataAssignObserver::PAYMENT_TYPE_CODE => 'visa',
            DataAssignObserver::MASKED_CARD_NUMBER => '4111xxxxxxxx1111'
        ];

        $eventTokenData = $data = new \Magento\Framework\DataObject([
            'token' => $additionalData[DataAssignObserver::TOKEN],
            'payment_type_code' => $additionalData[DataAssignObserver::PAYMENT_TYPE_CODE],
            'masked_card_number' => $additionalData[DataAssignObserver::MASKED_CARD_NUMBER],
            'customer_id' => $customerId
        ]);

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
            ->setMethods(['getAdditionalInformation','getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderModelMock = $this->getMockBuilder(Order::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->with('digitalhub_ebanx_global/cc', 'save')
            ->willReturn(0);

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventManagerMock = $this->getMockBuilder(Manager::class)
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentDOMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);

        $paymentModelMock->expects($this->once())
            ->method('getAdditionalInformation')
            ->willReturn($additionalData);

        $paymentModelMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderModelMock);

        $orderModelMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $eventManagerMock->expects($this->never())
            ->method('dispatch')
            ->with($this->anything());

        $validationSubject = [
            'response' => [
                'payment_result' => [
                    'status' => 'SUCCESS',
                    'payment' => [
                        'transaction_status' => [
                            'code' => 'OK'
                        ]
                    ]
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

        $validator = new AuthorizationValidator($resultFactory, $ebanxHelper, $logger, $eventManagerMock, $session);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }
}
