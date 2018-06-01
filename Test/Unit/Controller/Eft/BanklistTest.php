<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\Eft;

class BanklistTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $api_endpoint = 'https://sandbox.ebanx.com/ws/getBankList';
        $sandboxIntegrationKey = '05394750934750437503509';
        $ebanxBanklistResultJson = json_encode([
            [
                "code" => "banco_agrario",
                "name" => "BANCO AGRARIO"
            ],
            [
                "code" => "banco_av_villas",
                "name" => "BANCO AV VILLAS"
            ]
        ]);

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $curl = $this->getMockBuilder(\Magento\Framework\HTTP\Client\Curl::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->withConsecutive(
                ['digitalhub_ebanx_global', 'live_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox']
            )
            ->willReturnOnConsecutiveCalls(null, $sandboxIntegrationKey, 1);

        $curl->expects($this->once())
            ->method('setHeaders')
            ->with([
                'integration_key' => $sandboxIntegrationKey
            ]);

        $curl->expects($this->once())
            ->method('post')
            ->with($api_endpoint, [
                'integration_key' => $sandboxIntegrationKey,
                'operation' => 'request',
                'country' => 'co'
            ]);

        $curl->expects($this->once())
            ->method('getBody')
            ->willReturn($ebanxBanklistResultJson);

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'items' => [
                    [
                        "value" => "",
                        "label" => ""
                    ],
                    [
                        "value" => "banco_agrario",
                        "label" => "BANCO AGRARIO"
                    ],
                    [
                        "value" => "banco_av_villas",
                        "label" => "BANCO AV VILLAS"
                    ]
                ]
            ])
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\Eft\Banklist($context, $resultJsonFactory, $ebanxHelper, $curl);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }

    public function testExecuteWithError()
    {
        $api_endpoint = 'https://sandbox.ebanx.com/ws/getBankList';
        $sandboxIntegrationKey = '05394750934750437503509';

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $curl = $this->getMockBuilder(\Magento\Framework\HTTP\Client\Curl::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->withConsecutive(
                ['digitalhub_ebanx_global', 'live_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox_integration_key'],
                ['digitalhub_ebanx_global', 'sandbox']
            )
            ->willReturnOnConsecutiveCalls(null, $sandboxIntegrationKey, 1);

        $curl->expects($this->once())
            ->method('setHeaders')
            ->with([
                'integration_key' => $sandboxIntegrationKey
            ]);

        $curl->expects($this->once())
            ->method('post')
            ->with($api_endpoint, [
                'integration_key' => $sandboxIntegrationKey,
                'operation' => 'request',
                'country' => 'co'
            ]);

        $curl->expects($this->once())
            ->method('getBody')
            ->will($this->throwException(new \Exception));

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'items' => [
                    [
                        "value" => "",
                        "label" => ""
                    ]
                ]
            ])
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\Eft\Banklist($context, $resultJsonFactory, $ebanxHelper, $curl);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
