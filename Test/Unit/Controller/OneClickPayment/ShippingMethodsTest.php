<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class ShippingMethodsTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $quoteId = 1;
        $addressId = 1;
        $addressData = [];
        $arrayOfShippingRates = [
            new \Magento\Framework\DataObject([
                'method_title' => 'Flat Rate',
                'price' => 12,
                'code' => 'flatrate_flatrate'
            ]),
            new \Magento\Framework\DataObject([
                'method_title' => 'Free Shipping',
                'price' => 0,
                'code' => 'freeshipping_freeshipping'
            ])
        ];
        $baseSubtotal = 100;
        $baseGrandtotal = 100;

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
		$priceCurrency = $this->getMockBuilder(\Magento\Framework\Pricing\PriceCurrencyInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
		$paymentMethodManagement = $this->getMockBuilder(\Magento\Quote\Api\PaymentMethodManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $priceHelper = $this->getMockBuilder(\Magento\Framework\Pricing\Helper\Data::class)
            ->setMethods(['currency'])
            ->disableOriginalConstructor()
            ->getMock();

        $addressModel = $this->getMockBuilder(\Magento\Customer\Model\Address::class)
            ->setMethods(['load','getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getBillingAddress','getShippingAddress','getBaseSubtotal','getBaseGrandTotal','save'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteBillingAddress = $this->getMockBuilder(\Magento\Quote\Model\Quote\Address::class)
            ->setMethods(['addData'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteShippingAddress = $this->getMockBuilder(\Magento\Quote\Model\Quote\Address::class)
            ->setMethods(['addData','setCollectShippingRates','collectShippingRates','getShippingRatesCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteShippingAddress->expects($this->once())
            ->method('addData')
            ->with($addressData);

        $quoteShippingAddress->expects($this->once())
            ->method('addData')
            ->with($addressData);

        $quoteShippingAddress->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturn($quoteShippingAddress);

        $quoteShippingAddress->expects($this->once())
            ->method('collectShippingRates');

        $quoteShippingAddress->expects($this->once())
            ->method('getShippingRatesCollection')
            ->willReturn($arrayOfShippingRates);

        $quoteMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($quoteBillingAddress);

        $quoteMock->expects($this->any())
            ->method('getShippingAddress')
            ->willReturn($quoteShippingAddress);

        $quoteMock->expects($this->once())
            ->method('getBaseSubtotal')
            ->willReturn($baseSubtotal);

        $quoteMock->expects($this->once())
            ->method('getBaseGrandTotal')
            ->willReturn($baseGrandtotal);

        $quoteMock->expects($this->once())
            ->method('save');

        $priceHelper->expects($this->any())
            ->method('currency')
            ->withConsecutive(
                ['12.00', true, false],
                ['0.00', true, false]
            )
            ->willReturnOnConsecutiveCalls('$12.00', '$0.00');

        $requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods(['getParam'])
            ->getMockForAbstractClass();

        $requestMock->expects($this->any())
            ->method('getParam')
            ->withConsecutive(
                ['cart_id'],
                ['address_id']
            )
            ->willReturnOnConsecutiveCalls($quoteId, $addressId);

        $addressModel->expects($this->once())
            ->method('load')
            ->willReturn($addressModel);

        $addressModel->expects($this->once())
            ->method('getData')
            ->willReturn($addressData);

        $cartRepositoryInterface->expects($this->once())
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);

        $context->expects($this->any())
            ->method('getRequest')
            ->willReturn($requestMock);

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'success' => true,
    			'items' => [
                    ['label' => 'Flat Rate - $12.00', 'value' => 'flatrate_flatrate'],
                    ['label' => 'Free Shipping - $0.00', 'value' => 'freeshipping_freeshipping'],
                ],
    			'cart_id' => $quoteId,
    			'address_id' => $addressId,
    			'subtotal' => $baseSubtotal,
    			'total' => $baseGrandtotal
            ])
            ->willReturn($resultJson);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\ShippingMethods(
            $context,
            $resultJsonFactory,
            $ebanxHelper,
            $cartRepositoryInterface,
            $priceCurrency,
            $paymentMethodManagement,
            $priceHelper,
            $addressModel
        );

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
