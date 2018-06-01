<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\OneClickPayment;

class PaymentMethodsTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $quoteId = 1;
        $baseSubtotal = 100;
        $baseGrandtotal = 100;

        $rawJsonPost = json_encode([
            'cart_id' => $quoteId,
            'shipping_method' => 'flatrate_flatrate'
        ]);

        $context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->setMethods(['getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonFactory = $this->getMockBuilder(\Magento\Framework\Controller\Result\JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
		$ebanxHelper = $this->getMockBuilder(\DigitalHub\Ebanx\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
		$cartRepositoryInterface = $this->getMockBuilder(\Magento\Quote\Api\CartRepositoryInterface::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
		$paymentMethodManagement = $this->getMockBuilder(\Magento\Quote\Api\PaymentMethodManagementInterface::class)
            ->setMethods(['getList'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $arrayOfPaymentMethods = [
            new \Magento\Framework\DataObject([
                'code' => 'digitalhub_ebanx_brazil_creditcard',
                'title' => 'Credit Card'
            ]),
            new \Magento\Framework\DataObject([
                'code' => 'checkmo',
                'title' => 'Check/Money'
            ])
        ];

        $paymentMethodManagement->expects($this->once())
            ->method('getList')
            ->willReturn($arrayOfPaymentMethods);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn(new \Magento\Framework\DataObject([
                'content' => $rawJsonPost
            ]));

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getShippingAddress','collectTotals','save','getId','getBaseSubtotal','getBaseGrandTotal'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);

        $quoteMock->expects($this->once())
            ->method('getBaseSubtotal')
            ->willReturn($baseSubtotal);

        $quoteMock->expects($this->once())
            ->method('getBaseGrandTotal')
            ->willReturn($baseGrandtotal);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn(new \Magento\Framework\DataObject());

        $quoteMock->expects($this->once())
            ->method('collectTotals');

        $quoteMock->expects($this->once())
            ->method('save');

        $cartRepositoryInterface->expects($this->once())
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'success' => true,
    			'items' => [
                    ['title' => 'Credit Card', 'code' => 'digitalhub_ebanx_brazil_creditcard']
                ],
    			'cart_id' => $quoteId,
    			'subtotal' => $baseSubtotal,
    			'total' => $baseGrandtotal
            ])
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\PaymentMethods(
            $context,
            $resultJsonFactory,
            $ebanxHelper,
            $cartRepositoryInterface,
            $paymentMethodManagement
        );

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
