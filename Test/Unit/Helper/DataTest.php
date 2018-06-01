<?php
namespace DigitalHub\Ebanx\Test\Unit\Helper;

class DataTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigData()
    {
        $value = 1;
        $storeId = 1;

        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        $scopeConfigMock->method('getValue')
                ->with('payment/digitalhub_ebanx_global/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
                ->willReturn($value);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $this->assertEquals($value, $helper->getConfigData('digitalhub_ebanx_global','active', $storeId));
    }

    public function testGetPersonTypePersonalBr()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $this->assertEquals(
            \Ebanx\Benjamin\Models\Person::TYPE_PERSONAL,
            $helper->getPersonType('1234567890', 'BR')
        );
    }

    public function testGetPersonTypePersonalNot()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $this->assertEquals(
            \Ebanx\Benjamin\Models\Person::TYPE_PERSONAL,
            $helper->getPersonType('123456789037509347509', 'AR')
        );
    }

    public function testGetPersonTypeBusiness()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $this->assertEquals(
            \Ebanx\Benjamin\Models\Person::TYPE_BUSINESS,
            $helper->getPersonType('123456789037509347509', 'BR')
        );
    }

    public function testGetCustomerDocumentNumberTaxvat()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['getCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $customer->expects($this->once())
            ->method('getCustomAttribute')
            ->willReturn(null);

        $quote = new \Magento\Framework\DataObject([
            'customer' => $customer,
            'billing_address' => new \Magento\Framework\DataObject([
                'taxvat' => '1234567890'
            ])
        ]);

        $this->assertEquals(
            '1234567890',
            $helper->getCustomerDocumentNumber($quote, 'taxvat')
        );
    }

    public function testGetCustomerDocumentNumberTaxId()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['getCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $customer->expects($this->once())
            ->method('getCustomAttribute')
            ->willReturn(null);

        $quote = new \Magento\Framework\DataObject([
            'customer' => $customer,
            'billing_address' => new \Magento\Framework\DataObject([
                'vat_id' => '1234567890'
            ])
        ]);

        $this->assertEquals(
            '1234567890',
            $helper->getCustomerDocumentNumber($quote, 'taxvat')
        );
    }

    public function testGetCustomerDocumentNumberCustomAttribute()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['getCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $customer->expects($this->any())
            ->method('getCustomAttribute')
            ->with('cpf')
            ->willReturn(new \Magento\Framework\DataObject([
                'value' => '1234567890'
            ]));

        $quote = new \Magento\Framework\DataObject([
            'customer' => $customer,
            'billing_address' => new \Magento\Framework\DataObject([
                'vat_id' => '1234567890'
            ])
        ]);

        $this->assertEquals(
            '1234567890',
            $helper->getCustomerDocumentNumber($quote, 'cpf')
        );
    }

    public function testGetCustomerDocumentNumberBillingAddressData()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['getCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $customer->expects($this->any())
            ->method('getCustomAttribute')
            ->willReturn(null);

        $quote = new \Magento\Framework\DataObject([
            'customer' => $customer,
            'billing_address' => new \Magento\Framework\DataObject([
                'vat_id' => '1234567890'
            ])
        ]);

        $this->assertEquals(
            '1234567890',
            $helper->getCustomerDocumentNumber($quote, 'vat_id')
        );
    }

    public function testGetCustomerDocumentNumberCustomerData()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class
        );

        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['getCpf'])
            ->disableOriginalConstructor()
            ->getMock();

        $customer->expects($this->any())
            ->method('getCpf')
            ->willReturn('1234567890');

        $quote = new \Magento\Framework\DataObject([
            'customer' => $customer,
            'billing_address' => new \Magento\Framework\DataObject([
                'vat_id' => ''
            ])
        ]);

        $this->assertEquals(
            '1234567890',
            $helper->getCustomerDocumentNumber($quote, 'cpf')
        );
    }

    public function testGetCustomerDocumentNumberFieldBRCnpj()
    {
        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                ['payment/digitalhub_ebanx_global/customer_fields/document_field_brazil_cnpj', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null],
                ['payment/digitalhub_ebanx_global/customer_fields/document_field_brazil_cpf', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null]
            )
            ->willReturnOnConsecutiveCalls('taxvat', 'taxvat');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['getCustomAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $customer->expects($this->once())
            ->method('getCustomAttribute')
            ->willReturn(null);

        $quote = new \Magento\Framework\DataObject([
            'customer' => $customer,
            'billing_address' => new \Magento\Framework\DataObject([
                'country_id' => 'BR',
                'taxvat' => '1234567890'
            ])
        ]);

        $result = $helper->getCustomerDocumentNumberField($quote);

        $this->assertEquals(
            'taxvat',
            $result
        );
    }

    public function testGetCustomerDocumentNumberFieldBRCpf()
    {
        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                ['payment/digitalhub_ebanx_global/customer_fields/document_field_brazil_cnpj', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null],
                ['payment/digitalhub_ebanx_global/customer_fields/document_field_brazil_cpf', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null]
            )
            ->willReturnOnConsecutiveCalls(null, 'taxvat');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $quote = new \Magento\Framework\DataObject([
            'billing_address' => new \Magento\Framework\DataObject([
                'country_id' => 'BR',
                'taxvat' => '1234567890'
            ])
        ]);

        $result = $helper->getCustomerDocumentNumberField($quote);

        $this->assertEquals(
            'taxvat',
            $result
        );
    }

    public function testGetCustomerDocumentNumberFieldAR()
    {
        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with('payment/digitalhub_ebanx_global/customer_fields/document_field_argentina', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null)
            ->willReturn('taxvat');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $quote = new \Magento\Framework\DataObject([
            'billing_address' => new \Magento\Framework\DataObject([
                'country_id' => 'AR',
                'taxvat' => '1234567890'
            ])
        ]);

        $result = $helper->getCustomerDocumentNumberField($quote);

        $this->assertEquals(
            'taxvat',
            $result
        );
    }

    public function testGetCustomerDocumentNumberFieldCL()
    {
        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with('payment/digitalhub_ebanx_global/customer_fields/document_field_chile', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null)
            ->willReturn('taxvat');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $quote = new \Magento\Framework\DataObject([
            'billing_address' => new \Magento\Framework\DataObject([
                'country_id' => 'CL',
                'taxvat' => '1234567890'
            ])
        ]);

        $result = $helper->getCustomerDocumentNumberField($quote);

        $this->assertEquals(
            'taxvat',
            $result
        );
    }

    public function testGetCustomerDocumentNumberFieldCO()
    {
        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with('payment/digitalhub_ebanx_global/customer_fields/document_field_colombia', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null)
            ->willReturn('taxvat');

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $quote = new \Magento\Framework\DataObject([
            'billing_address' => new \Magento\Framework\DataObject([
                'country_id' => 'CO',
                'taxvat' => '1234567890'
            ])
        ]);

        $result = $helper->getCustomerDocumentNumberField($quote);

        $this->assertEquals(
            'taxvat',
            $result
        );
    }

    /**
     * @dataProvider dataProviderGetAddressData
     */
    public function testGetAddressData($requested, $expected)
    {
        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with('payment/digitalhub_ebanx_global/customer_fields/' . $requested['field'], \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null)
            ->willReturn($requested['field_config']);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $billingAddress = $this->getMockBuilder(\Magento\Quote\Model\Quote\Address::class)
            ->disableOriginalConstructor()
            ->getMock();

        $billingAddress->expects($this->once())
            ->method('getData')
            ->willReturn($requested['billing_address']);

        $billingAddress->expects($this->any())
            ->method('getStreetLine')
            ->willReturn($expected);

        $this->assertEquals(
            $expected,
            $helper->getAddressData($requested['field'], $billingAddress)
        );
    }

    public function dataProviderGetAddressData()
    {
        return [
            'street' => [
                'requested' => [
                    'field' => 'street',
                    'field_config' => 'street_1',
                    'billing_address' => [
                        'street' => ['Rua de teste', '123', 'Complemento', 'Bairro'],
                        'street_number' => 123,
                        'bairro' => 'Centro'
                    ]
                ],
                'expected' => 'Rua de teste'
            ],
            'custom_complement' => [
                'requested' => [
                    'field' => 'complement',
                    'field_config' => 'custom_complement',
                    'billing_address' => [
                        'street' => ['Rua de teste', '123', 'Complemento', 'Bairro'],
                        'street_number' => 123,
                        'custom_complement' => 'Complemento'
                    ]
                ],
                'expected' => 'Complemento'
            ],
            'not_exists' => [
                'requested' => [
                    'field' => 'street_number',
                    'field_config' => 'teste',
                    'billing_address' => [
                        'street' => ['Rua de teste', '123', 'Complemento', 'Bairro'],
                        'street_number' => 123,
                        'custom_complement' => 'Complemento'
                    ]
                ],
                'expected' => null
            ]
        ];
    }

    /**
     * @dataProvider dataProviderGetInterestRateFor
     */
    public function testGetInterestRateFor($requested, $expected, $config)
    {
        $jsonInterestRates = json_encode($config);

        $scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->disableOriginalConstructor()
                ->getMock();

        $scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                ['payment/digitalhub_ebanx_global/cc/enable_interest_rate', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null],
                ['payment/digitalhub_ebanx_global/cc/interest_rates', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null]
            )
            ->willReturnOnConsecutiveCalls(1, $jsonInterestRates);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $helper = $objectManager->getObject(
            \DigitalHub\Ebanx\Helper\Data::class,
            ['scopeConfig' => $scopeConfigMock]
        );

        $this->assertEquals(
            $expected,
            $helper->getInterestRateFor($requested['number'])
        );

        $this->assertEquals(
            true,
            is_numeric($helper->getInterestRateFor($requested['number']))
        );
    }

    public function dataProviderGetInterestRateFor()
    {
        return [
            [
                'requested' => [
                    'number' => 6
                ],
                'expected' => 5.99,
                'config' => [
                    ['number' => 1, 'value' => 0],
                    ['number' => 2, 'value' => 0],
                    ['number' => 3, 'value' => 0],
                    ['number' => 4, 'value' => 0],
                    ['number' => 5, 'value' => 0],
                    ['number' => 6, 'value' => 5.99],
                    ['number' => 7, 'value' => 6.99],
                    ['number' => 8, 'value' => 7.99],
                    ['number' => 9, 'value' => 0],
                    ['number' => 10, 'value' => 0],
                    ['number' => 11, 'value' => 10],
                    ['number' => 12, 'value' => 20],
                ]
            ],
            [
                'requested' => [
                    'number' => 2
                ],
                'expected' => 0,
                'config' => [
                    ['number' => 1, 'value' => 0],
                    ['number' => 2, 'value' => 0],
                    ['number' => 3, 'value' => 0],
                    ['number' => 4, 'value' => 0],
                    ['number' => 5, 'value' => 0],
                    ['number' => 6, 'value' => 5.99],
                    ['number' => 7, 'value' => 6.99],
                    ['number' => 8, 'value' => 7.99],
                    ['number' => 9, 'value' => 0],
                    ['number' => 10, 'value' => 0],
                    ['number' => 11, 'value' => 10],
                    ['number' => 12, 'value' => 20],
                ]
            ],
            [
                'requested' => [
                    'number' => 12
                ],
                'expected' => 20,
                'config' => [
                    ['number' => 1, 'value' => 0],
                    ['number' => 2, 'value' => 0],
                    ['number' => 3, 'value' => 0],
                    ['number' => 4, 'value' => 0],
                    ['number' => 5, 'value' => 0],
                    ['number' => 6, 'value' => 5.99],
                    ['number' => 7, 'value' => 6.99],
                    ['number' => 8, 'value' => 7.99],
                    ['number' => 9, 'value' => 0],
                    ['number' => 10, 'value' => 0],
                    ['number' => 11, 'value' => 10],
                    ['number' => 12, 'value' => 20],
                ]
            ],
            [
                'requested' => [
                    'number' => 12
                ],
                'expected' => 0,
                'config' => [
                    ['number' => 1, 'value' => 0],
                    ['number' => 2, 'value' => 0],
                    ['number' => 3, 'value' => 0],
                    ['number' => 4, 'value' => 0],
                    ['number' => 5, 'value' => 0]
                ]
            ]
        ];
    }

    public function testCalculateTotalWithInterest()
    {
        $helper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->setMethods(['getInterestRateFor'])
            ->disableOriginalConstructor()
            ->getMock();

        $helper->expects($this->any())
            ->method('getInterestRateFor')
            ->withConsecutive(
                [2],
                [1],
                [12]
            )
            ->willReturnOnConsecutiveCalls(0,0,20);

        $this->assertEquals(
            100,
            $helper->calculateTotalWithInterest(100, 2)
        );

        $this->assertEquals(
            200,
            $helper->calculateTotalWithInterest(200, 1)
        );

        $this->assertEquals(
            240,
            $helper->calculateTotalWithInterest(200, 12)
        );
    }
}
