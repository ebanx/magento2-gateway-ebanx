<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Validator\Ecuador\SafetyPay;

use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Logger\Logger;
use DigitalHub\Ebanx\Gateway\Validator\Ecuador\SafetyPay\CountryValidator;

class CountryValidatorTest extends \PHPUnit\Framework\TestCase
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

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn(new \Magento\Framework\DataObject([
                'base_currency_code' => 'USD'
            ]));

        $validationSubject = [
            'country' => 'EC',
            'storeId' => 1
        ];

        $resultFactory->expects($this->once())
            ->method('create')
            ->with([
                'isValid' => true,
                'failsDescription' => []
            ])
            ->willReturn($resultMock);

        $validator = new CountryValidator($resultFactory, $ebanxHelper, $storeManager);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }

    public function testFail()
    {
        $expectation = [];

        $resultMock = $this->getMockBuilder(ResultInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactory = $this->getMockBuilder(\Magento\Payment\Gateway\Validator\ResultInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn(new \Magento\Framework\DataObject([
                'base_currency_code' => 'USD'
            ]));

        $validationSubject = [
            'country' => 'PE',
            'storeId' => 1
        ];

        $resultFactory->expects($this->once())
            ->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => []
            ])
            ->willReturn($resultMock);

        $validator = new CountryValidator($resultFactory, $ebanxHelper, $storeManager);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }

    public function testFailCurrency()
    {
        $expectation = [];

        $resultMock = $this->getMockBuilder(ResultInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactory = $this->getMockBuilder(\Magento\Payment\Gateway\Validator\ResultInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn(new \Magento\Framework\DataObject([
                'base_currency_code' => 'ARS'
            ]));

        $validationSubject = [
            'country' => 'EC',
            'storeId' => 1
        ];

        $resultFactory->expects($this->once())
            ->method('create')
            ->with([
                'isValid' => false,
                'failsDescription' => []
            ])
            ->willReturn($resultMock);

        $validator = new CountryValidator($resultFactory, $ebanxHelper, $storeManager);
        $result = $validator->validate($validationSubject);

        $this->assertInstanceOf(
            ResultInterface::class,
            $result
        );
    }
}
