<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\OneClickPayment;

class AddressTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $customerId = 1;
        $address1 = [
            'id' => 1,
            'street' => ['Rua de teste', '123'],
            'city' => 'Curitiba',
            'region_code' => 'PR',
            'country' => 'Brasil',
            'postcode' => '80010-010'
        ];
        $address2 = [
            'id' => 2,
            'street' => ['Rua de teste', '456'],
            'city' => 'Londrina',
            'region_code' => 'PR',
            'country' => 'Brasil',
            'postcode' => '81111-111'
        ];
        $addresses = [
            new \Magento\Framework\DataObject($address1),
            new \Magento\Framework\DataObject($address2)
        ];

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
		$session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();
		$customerFactory = $this->getMockBuilder(\Magento\Customer\Model\CustomerFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $session->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $customerMock = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['load','getAddresses'])
            ->disableOriginalConstructor()
            ->getMock();

        $customerMock->expects($this->once())
            ->method('load')
            ->with($customerId)
            ->willReturn($customerMock);

        $customerMock->expects($this->once())
            ->method('getAddresses')
            ->willReturn($addresses);

        $customerFactory->expects($this->once())
            ->method('create')
            ->willReturn($customerMock);

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'success' => true,
                'items' => [
                    [
                        'label' => 'Rua de teste, 123, Curitiba, PR, Brasil, 80010-010',
                        'value' => 1
                    ],
                    [
                        'label' => 'Rua de teste, 456, Londrina, PR, Brasil, 81111-111',
                        'value' => 2
                    ]
                ]
            ])
            ->willReturn($resultJson);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\Address($context, $resultJsonFactory, $ebanxHelper, $session, $customerFactory);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
