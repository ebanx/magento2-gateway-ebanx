<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\Checkout;

class ExchangeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider currencyDataProvider
     */
    public function testExecute($requested, $expected)
    {
        $api_endpoint = 'https://sandbox.ebanx.com/ws/exchange';
        $sandboxIntegrationKey = '0593405934707097053945';
        $ebanxExchangeResultJson = json_encode([
            "currency_rate" => [
                "code" => "USD",
                "base_code" => $requested['to_currency'],
                "name" => "Real to US Dollar",
                "rate" => $requested['rate']
            ],
            "status" => "SUCCESS"
        ]);

        $amountTotal = $requested['total'];
        $totalWithIof = $amountTotal + ($amountTotal*0.0038);
        $amountTotalConverted = $amountTotal * $requested['rate'];
        $amountTotalConvertedWithIof = $amountTotalConverted + ($amountTotalConverted * 0.0038);

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curl = $this->getMockBuilder(\Magento\Framework\HTTP\Client\Curl::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency = $this->getMockBuilder(\Magento\Framework\Locale\CurrencyInterface::class)
            ->setMethods(['getCurrency','toCurrency','getDefaultCurrency'])
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn(new \Magento\Framework\DataObject([
                'param' => 1
            ]));

        $currency->expects($this->any())
            ->method('getCurrency')
            ->with($requested['to_currency'])
            ->willReturn($currency);

        $currency->expects($this->any())
            ->method('getDefaultCurrency')
            ->willReturn($currency);

        $currency->expects($this->any())
            ->method('toCurrency')
            ->withConsecutive(
                [$amountTotalConverted],
                [$amountTotalConvertedWithIof]
            )
            ->willReturnOnConsecutiveCalls('$' . number_format($amountTotalConverted,2), '$' . number_format($amountTotalConvertedWithIof,2));

        $curl->expects($this->once())
            ->method('setHeaders')
            ->with([
                'integration_key' => $sandboxIntegrationKey
            ]);

        $curl->expects($this->once())
            ->method('post')
            ->with($api_endpoint, [
                'integration_key' => $sandboxIntegrationKey,
                'currency_code' => 'USD',
                'currency_base_code' => $requested['to_currency']
            ]);

        $curl->expects($this->once())
            ->method('getBody')
            ->willReturn($ebanxExchangeResultJson);

        $fakeCurrencyMock = new \Magento\Framework\DataObject();
        $fakeCurrencyMock->setData([
            'code' => 'USD'
        ]);

        $fakeStoreMock = new \Magento\Framework\DataObject();
        $fakeStoreMock->setData([
            'current_currency' => $fakeCurrencyMock
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
            ->willReturnOnConsecutiveCalls(null, $sandboxIntegrationKey, true);

        $billingAddressMock = new \Magento\Framework\DataObject();
        $billingAddressMock->setData([
            'country_id' => $requested['country']
        ]);

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getBaseGrandTotal', 'getBillingAddress'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock->expects($this->once())
            ->method('getBaseGrandTotal')
            ->willReturn($amountTotal);

        $quoteMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($billingAddressMock);

        $session->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $jsonMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $jsonMock->expects($this->any())
            ->method('setData')
            ->with([
                'success' => true,
                'total_formatted' => $expected['total_formatted'],
                'total_with_iof_formatted' => $expected['total_with_iof_formatted'],
                'currency' => $expected['currency']
            ])
            ->willReturn($jsonMock);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);

        $controller = new \DigitalHub\Ebanx\Controller\Checkout\Exchange($context, $resultJsonFactory, $ebanxHelper, $session, $storeManager, $logger, $curl, $currency);

        $this->assertSame(
            $jsonMock,
            $controller->execute()
        );
    }

    public function currencyDataProvider()
    {
        return [
            'BRL' => [
                'requested' => [
                    'to_currency' => 'BRL',
                    'total' => 123.45,
                    'rate' => 2,
                    'country' => 'BR'
                ],
                'expected' => [
                    'total_formatted' => '$246.90',
                    'total_with_iof_formatted' => '$247.84',
                    'currency' => 'BRL'
                ]
            ],
            'ARS' => [
                'requested' => [
                    'to_currency' => 'ARS',
                    'total' => 100,
                    'rate' => 20,
                    'country' => 'AR'
                ],
                'expected' => [
                    'total_formatted' => '$2,000.00',
                    'total_with_iof_formatted' => null,
                    'currency' => 'ARS'
                ]
            ],
            'CLP' => [
                'requested' => [
                    'to_currency' => 'CLP',
                    'total' => 100,
                    'rate' => 30,
                    'country' => 'CL'
                ],
                'expected' => [
                    'total_formatted' => '$3,000.00',
                    'total_with_iof_formatted' => null,
                    'currency' => 'CLP'
                ]
            ],
            'COP' => [
                'requested' => [
                    'to_currency' => 'COP',
                    'total' => 100,
                    'rate' => 10,
                    'country' => 'CO'
                ],
                'expected' => [
                    'total_formatted' => '$1,000.00',
                    'total_with_iof_formatted' => null,
                    'currency' => 'COP'
                ]
            ],
            'MX' => [
                'requested' => [
                    'to_currency' => 'MXN',
                    'total' => 100,
                    'rate' => 10,
                    'country' => 'MX'
                ],
                'expected' => [
                    'total_formatted' => '$1,000.00',
                    'total_with_iof_formatted' => null,
                    'currency' => 'MXN'
                ]
            ],
            'PEN' => [
                'requested' => [
                    'to_currency' => 'PEN',
                    'total' => 100,
                    'rate' => 2,
                    'country' => 'PE'
                ],
                'expected' => [
                    'total_formatted' => '$200.00',
                    'total_with_iof_formatted' => null,
                    'currency' => 'PEN'
                ]
            ],
            'EC_USD' => [
                'requested' => [
                    'to_currency' => 'USD',
                    'total' => 100,
                    'rate' => 2,
                    'country' => 'EC'
                ],
                'expected' => [
                    'total_formatted' => '$200.00',
                    'total_with_iof_formatted' => null,
                    'currency' => 'USD'
                ]
            ]
        ];
    }

    public function testExecuteWithError()
    {
        $api_endpoint = 'https://sandbox.ebanx.com/ws/exchange';
        $sandboxIntegrationKey = '0593405934707097053945';
        $ebanxExchangeResultJson = json_encode([
            "currency_rate" => [
                "code" => "USD",
                "base_code" => "BRL",
                "name" => "Real to US Dollar"
            ],
            "status" => "SUCCESS"
        ]);

        $amountTotal = 123.45;
        $totalWithIof = $amountTotal + ($amountTotal*0.0038);
        $amountTotalConverted = $amountTotal * 2.7731;
        $amountTotalConvertedWithIof = $amountTotalConverted + ($amountTotalConverted * 0.0038);

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $curl = $this->getMockBuilder(\Magento\Framework\HTTP\Client\Curl::class)
            ->disableOriginalConstructor()
            ->getMock();
        $currency = $this->getMockBuilder(\Magento\Framework\Locale\CurrencyInterface::class)
            ->setMethods(['getCurrency','toCurrency','getDefaultCurrency'])
            ->disableOriginalConstructor()
            ->getMock();

        $currency->expects($this->any())
            ->method('getCurrency')
            ->with('BRL')
            ->willReturn($currency);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn(new \Magento\Framework\DataObject([
                'param' => 1
            ]));

        $currency->expects($this->any())
            ->method('getDefaultCurrency')
            ->willReturn($currency);

        $currency->expects($this->any())
            ->method('toCurrency')
            ->withConsecutive(
                [$amountTotalConverted],
                [$amountTotalConvertedWithIof]
            )
            ->willReturnOnConsecutiveCalls('R$123,45', 'R$123,92');

        $curl->expects($this->once())
            ->method('setHeaders')
            ->with([
                'integration_key' => $sandboxIntegrationKey
            ]);

        $curl->expects($this->once())
            ->method('post')
            ->with($api_endpoint, [
                'integration_key' => $sandboxIntegrationKey,
                'currency_code' => 'USD',
                'currency_base_code' => 'BRL'
            ]);

        $curl->expects($this->once())
            ->method('getBody')
            ->willReturn($ebanxExchangeResultJson);

        $fakeCurrencyMock = new \Magento\Framework\DataObject();
        $fakeCurrencyMock->setData([
            'code' => 'USD'
        ]);

        $fakeStoreMock = new \Magento\Framework\DataObject();
        $fakeStoreMock->setData([
            'current_currency' => $fakeCurrencyMock
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
            ->willReturnOnConsecutiveCalls(null, $sandboxIntegrationKey, true);

        $billingAddressMock = new \Magento\Framework\DataObject();
        $billingAddressMock->setData([
            'country_id' => 'BR'
        ]);

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getBaseGrandTotal', 'getBillingAddress'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock->expects($this->once())
            ->method('getBaseGrandTotal')
            ->willReturn($amountTotal);

        $quoteMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($billingAddressMock);

        $session->expects($this->any())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $jsonMock = new \Magento\Framework\DataObject();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);

        $controller = new \DigitalHub\Ebanx\Controller\Checkout\Exchange($context, $resultJsonFactory, $ebanxHelper, $session, $storeManager, $logger, $curl, $currency);

        $result = $controller->execute();

        $this->assertSame(
            $jsonMock['error'],
            $result['error']
        );

        $this->assertArrayHasKey(
            'message',
            $result
        );
    }
}
