<?php
namespace DigitalHub\Ebanx\Test\Unit\Gateway\Http\Client\Peru\SafetyPay;

use Magento\Payment\Model\Method\Logger;
use Magento\Payment\Gateway\Http\TransferInterface;
use DigitalHub\Ebanx\Gateway\Http\Client\Peru\SafetyPay\TransactionAuthorization;

class TransactionAuthorizationTest extends \PHPUnit\Framework\TestCase
{
    const SANDBOX_INTEGRATION_KEY = '09347509347509347504975037';
    public $fakeEbanxFacade;

    public function testPlaceRequestSuccess()
    {
        \Mockery::close(); // to "re-overload" classes

        $expectedRequest = [
            'type' => 'safetypay'
        ];

        $expectedResponse = [
            'payment_result' => [
                'status' => 'SUCCESS',
                'payment' => [
                    'hash' => '320947230497327409374'
                ]
            ]
        ];

        // DI constructor args
        $context = $this->getMockBuilder(\Magento\Framework\Model\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $encryptor = $this->getMockBuilder(\Magento\Framework\Encryption\EncryptorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();
        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $data = [];

        // methods
        $fakeStoreMock = new \Magento\Framework\DataObject();
        $fakeStoreMock->setData([
            'base_currency_code' => 'USD'
        ]);

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($fakeStoreMock);

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->withConsecutive(
                ['digitalhub_ebanx_global', 'live_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox']
            )
            ->willReturnOnConsecutiveCalls(null, self::SANDBOX_INTEGRATION_KEY, 1);

        $fakeEbanxFacade = \Mockery::mock('overload:Ebanx\Benjamin\Facade');
        $fakeEbanxFacade->shouldReceive('addConfig')
            ->withAnyArgs()
            ->andReturn($fakeEbanxFacade);

        $transactionClient = new TransactionAuthorization($context, $encryptor, $logger, $ebanxHelper, $storeManager, $data);

        $fakeEbanxFacade->shouldReceive('create')
            ->withAnyArgs()
            ->once()
            ->andReturn([
                'status' => 'SUCCESS',
                'payment' => [
                    'hash' => '320947230497327409374'
                ]
            ])
            ->byDefault();

        $transferObject = $this->getMockBuilder(TransferInterface::class)->getMock();
        $transferObject->expects($this->any())
            ->method('getBody')
            ->willReturn($expectedRequest);

        $this->assertEquals(
            $expectedResponse,
            $transactionClient->placeRequest($transferObject)
        );
    }

    public function testPlaceRequestError()
    {
        \Mockery::close(); // to "re-overload" classes

        $expectedRequest = [
            'type' => 'safetypay'
        ];

        $expectedResponse = [
            'error' => 'Error Message'
        ];

        // DI constructor args
        $context = $this->getMockBuilder(\Magento\Framework\Model\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $encryptor = $this->getMockBuilder(\Magento\Framework\Encryption\EncryptorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->setMethods(['getConfigData'])
            ->disableOriginalConstructor()
            ->getMock();
        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $data = [];

        // methods
        $fakeStoreMock = new \Magento\Framework\DataObject();
        $fakeStoreMock->setData([
            'base_currency_code' => 'USD'
        ]);

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($fakeStoreMock);

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->withConsecutive(
                ['digitalhub_ebanx_global', 'live_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox']
            )
            ->willReturnOnConsecutiveCalls(null, self::SANDBOX_INTEGRATION_KEY, 1);

        $fakeEbanxFacade = \Mockery::mock('overload:Ebanx\Benjamin\Facade');
        $fakeEbanxFacade->shouldReceive('addConfig')
            ->withAnyArgs()
            ->andReturn($fakeEbanxFacade);

        $transactionClient = new TransactionAuthorization($context, $encryptor, $logger, $ebanxHelper, $storeManager, $data);

        $fakeEbanxFacade->shouldReceive('create')
            ->withAnyArgs()
            ->once()
            ->andThrow(\Exception::class, 'Error Message')
            ->byDefault();

        $transferObject = $this->getMockBuilder(TransferInterface::class)->getMock();
        $transferObject->expects($this->any())
            ->method('getBody')
            ->willReturn($expectedRequest);

        $this->assertEquals(
            $expectedResponse,
            $transactionClient->placeRequest($transferObject)
        );
    }
}
