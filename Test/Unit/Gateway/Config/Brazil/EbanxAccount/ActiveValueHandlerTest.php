<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Config\Brazil\EbanxAccount;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;

use DigitalHub\Ebanx\Gateway\Config\Brazil\EbanxAccount\ActiveValueHandler;

use DigitalHub\Ebanx\Helper\Data;
use DigitalHub\Ebanx\Logger\Logger;

class ActiveValueHandlerTest extends \PHPUnit\Framework\TestCase
{

    /**
    * @dataProvider handleDataProvider
    */
    public function testHandle($expectedConfig, $expectedResult)
    {
        $subject = [];
        $storeId = $expectedConfig['storeId'];
        $enabled_payments = $expectedConfig['config_enabled_payments'];

        $expectation = $expectedResult;

        $ebanxHelper = $this->getMockBuilder(Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $fakeBaseCurrencyMock = new \Magento\Framework\DataObject();
        $fakeBaseCurrencyMock->setData([
            'code' => $expectedConfig['currency']
        ]);

        $fakeStoreMock = new \Magento\Framework\DataObject();
        $fakeStoreMock->setData([
            'base_currency' => $fakeBaseCurrencyMock
        ]);

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($fakeStoreMock);

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->withConsecutive(
                ['digitalhub_ebanx_global', 'active', $storeId],
                ['digitalhub_ebanx_global', 'payments_brazil', $storeId]
            )
            ->willReturnOnConsecutiveCalls(true, $enabled_payments);

        $valueHandler = new ActiveValueHandler($ebanxHelper, $session, $storeManager, $logger);
        $this->assertEquals(
            $expectation,
            $valueHandler->handle($subject, $storeId)
        );
    }

    public function handleDataProvider()
    {
        return [
            [
                'expectedConfig' => [
                    'storeId' => 1,
                    'currency' => 'USD',
                    'config_enabled_payments' => 'boleto,creditcard,tef,ebanxaccount'
                ],
                'expectedResult' => true
            ],
            [
                'expectedConfig' => [
                    'storeId' => 2,
                    'currency' => 'USD',
                    'config_enabled_payments' => 'boleto,creditcard,tef,ebanxaccount'
                ],
                'expectedResult' => true
            ],
            [
                'expectedConfig' => [
                    'storeId' => 1,
                    'currency' => 'USD',
                    'config_enabled_payments' => 'creditcard,tef'
                ],
                'expectedResult' => false
            ],
            [
                'expectedConfig' => [
                    'storeId' => 1,
                    'currency' => 'BRL',
                    'config_enabled_payments' => 'creditcard,tef,ebanxaccount'
                ],
                'expectedResult' => false
            ],
            [
                'expectedConfig' => [
                    'storeId' => 1,
                    'currency' => 'MXN',
                    'config_enabled_payments' => 'boleto'
                ],
                'expectedResult' => false
            ]
        ];
    }
}
