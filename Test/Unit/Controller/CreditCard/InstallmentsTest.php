<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\CreditCard;

class InstallmentsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testExecute($requested, $expected)
    {
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

        $priceCurrency = $this->getMockBuilder(\Magento\Framework\Pricing\PriceCurrencyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();


        $jsonMock = new \Magento\Framework\DataObject();
        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($jsonMock);

        $ebanxHelper->expects($this->any())
            ->method('getConfigData')
            ->withConsecutive(
                ['digitalhub_ebanx_global/cc', 'max_installments'],
                ['digitalhub_ebanx_global/cc', 'min_installment_value']
            )
            ->willReturnOnConsecutiveCalls($requested['max_installments'],$requested['min_installment_value']);

        $ebanxHelper->expects($this->any())
            ->method('getInterestRateFor')
            ->willReturn(0);

        $ebanxHelper->expects($this->any())
            ->method('calculateTotalWithInterest')
            ->withConsecutive(
                [$requested['total'], 1],
                [$requested['total'], 2],
                [$requested['total'], 3],
                [$requested['total'], 4],
                [$requested['total'], 5],
                [$requested['total'], 6],
                [$requested['total'], 7],
                [$requested['total'], 8],
                [$requested['total'], 9],
                [$requested['total'], 10],
                [$requested['total'], 11],
                [$requested['total'], 12]
            )
            ->willReturnOnConsecutiveCalls(
                $requested['total'], // 1
                $requested['total'], // 2
                $requested['total'], // 3
                $requested['total'], // 4
                $requested['total'], // 5
                $requested['total'], // 6
                $requested['total'], // 7
                $requested['total'], // 8
                $requested['total'], // 9
                $requested['total'], // 10
                $requested['total'], // 12
                $requested['total'] // 11
            );

        $requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getParam'])
            ->getMockForAbstractClass();

        $requestMock->expects($this->any())
            ->method('getParam')
            ->with('total', 0)
            ->willReturn($requested['total']);

        $context->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $controller = new \DigitalHub\Ebanx\Controller\CreditCard\Installments($context, $resultJsonFactory, $ebanxHelper, $priceCurrency);

        $this->assertEquals(
            $jsonMock,
            $controller->execute()
        );

        $this->assertEquals(
            $jsonMock->getData(),
            $expected
        );
    }

    public function dataProvider()
    {
        return [
            [
                'requested' => [
                    'max_installments' => 12,
                    'min_installment_value' => 10,
                    'total' => '100'
                ],
                'expected' => [
                    'success' => true,
                    'installments' => [
                        [
                            'number' => 1,
                            'installment_value' => 100,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 2,
                            'installment_value' => 50,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 3,
                            'installment_value' => 33.333333333333336,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 4,
                            'installment_value' => 25,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 5,
                            'installment_value' => 20,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 6,
                            'installment_value' => 16.666666666666668,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 7,
                            'installment_value' => 14.285714285714286,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 8,
                            'installment_value' => 12.5,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 9,
                            'installment_value' => 11.11111111111111,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ],
                        [
                            'number' => 10,
                            'installment_value' => 10,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ]
                    ]
                ]
            ],
            [
                'requested' => [
                    'max_installments' => 12,
                    'min_installment_value' => 10,
                    'total' => '0'
                ],
                'expected' => [
                    'success' => false,
                    'installments' => []
                ]
            ],
            [
                'requested' => [
                    'max_installments' => null,
                    'min_installment_value' => 10,
                    'total' => '100'
                ],
                'expected' => [
                    'success' => true,
                    'installments' => [
                        [
                            'number' => 1,
                            'installment_value' => 100,
                            'total_with_interest' => 100,
                            'interest' => 0
                        ]
                    ]
                ]
            ]
        ];
    }
}
