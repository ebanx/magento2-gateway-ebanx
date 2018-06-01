<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\CreditCard\Checkout;

class SavedTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $expected = [
            'customer_id' => 1,
            'payment_method' => 'digitalhub_ebanx_brazil_creditcard',
            'token' => [
                [
                    'id' => 1,
                    'masked_card_number' => '4111xxxxxxxx1111',
                    'payment_type_code' => 'visa',
                    'payment_method' => 'digitalhub_ebanx_brazil_creditcard'
                ]
            ]
        ];

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getParam'])
            ->getMockForAbstractClass();

        $requestMock->expects($this->once())
            ->method('getParam')
            ->with('method')
            ->willReturn($expected['payment_method']);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

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

        $tokenFactory = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\TokenFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $tokenModelMock = $this->getMockBuilder(\DigitalHub\Ebanx\Model\CreditCard\Token::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tokenObject = new \Magento\Framework\DataObject();
        $tokenObject->setData($expected['token'][0]);

        $collectionItems = [
            $tokenObject
        ];

        $tokenCollectionMock = $objectManager->getCollectionMock(\DigitalHub\Ebanx\Model\ResourceModel\CreditCard\Token\Collection::class, $collectionItems);

        $tokenCollectionMock->expects($this->any())
            ->method('addFieldToFilter')
            ->withConsecutive(
                ['customer_id', $expected['customer_id']],
                ['payment_method', $expected['payment_method']]
            );

        $tokenModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($tokenCollectionMock);

        $tokenFactory->expects($this->once())
            ->method('create')
            ->willReturn($tokenModelMock);

        $session->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($expected['customer_id']);

        $resultJson = new \Magento\Framework\DataObject();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\CreditCard\Checkout\Saved($context, $resultJsonFactory, $ebanxHelper, $session, $tokenFactory);

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );

        $this->assertEquals(
            $resultJson->getData('items')[0],
            $expected['token'][0]
        );
    }
}
