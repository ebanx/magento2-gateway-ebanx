<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Config\Brazil\CreditCard;

use DigitalHub\Ebanx\Gateway\Config\Brazil\CreditCard\ActiveValueHandler;

use DigitalHub\Ebanx\Helper\Data;
use Magento\Checkout\Model\Session;
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

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->withConsecutive(
                ['digitalhub_ebanx_global', 'active', $storeId],
                ['digitalhub_ebanx_global', 'payments_brazil', $storeId]
            )
            ->willReturnOnConsecutiveCalls(true, $enabled_payments);

        $valueHandler = new ActiveValueHandler($ebanxHelper, $session, $logger);
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
                    'config_enabled_payments' => 'boleto,creditcard,tef'
                ],
                'expectedResult' => true
            ],
            [
                'expectedConfig' => [
                    'storeId' => 2,
                    'config_enabled_payments' => 'boleto,creditcard,tef'
                ],
                'expectedResult' => true
            ],
            [
                'expectedConfig' => [
                    'storeId' => 1,
                    'config_enabled_payments' => 'tef'
                ],
                'expectedResult' => false
            ]
        ];
    }
}
