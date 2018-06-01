<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class InitQuoteTest extends \PHPUnit\Framework\TestCase
{
    public function testExexuteSimpleProduct()
    {
        $quoteId = 1;
        $customerId = 1;
        $baseSubtotal = 100;
        $subtotal = 100;
        $productId = 123;
        $productQty = 1;

        $rawJsonPost = json_encode([
            'product_id' => $productId,
            'product_qty' => $productQty
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
        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
        $cartRepositoryInterface = $this->getMockBuilder(\Magento\Quote\Api\CartRepositoryInterface::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $cartManagementInterface = $this->getMockBuilder(\Magento\Quote\Api\CartManagementInterface::class)
            ->setMethods(['createEmptyCart'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $customerFactory = $this->getMockBuilder(\Magento\Customer\Model\CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $customerRepository = $this->getMockBuilder(\Magento\Customer\Api\CustomerRepositoryInterface::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $order = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn(new \Magento\Framework\DataObject([
                'content' => $rawJsonPost
            ]));

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $product->expects($this->once())
            ->method('load')
            ->with($productId)
            ->willReturn($product);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getId','getBaseSubtotal','getSubtotal','setStore','setIsActive','assignCustomer','collectTotals','save','addProduct'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);

        $quoteMock->expects($this->once())
            ->method('getBaseSubtotal')
            ->willReturn($baseSubtotal);

        $quoteMock->expects($this->once())
            ->method('getSubtotal')
            ->willReturn($subtotal);

        $cartManagementInterface->expects($this->once())
            ->method('createEmptyCart')
            ->willReturn($quoteId);

        $cartRepositoryInterface->expects($this->once())
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);

        $session->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'cart_id' => $quoteId,
                'base_subtotal' => $baseSubtotal,
                'subtotal' => $subtotal
            ])
            ->willReturn($resultJson);

        $storeMock = $this->getMockBuilder(\Magento\Store\Model\Store::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $quoteMock->expects($this->once())
            ->method('setStore')
            ->with($storeMock);

        $quoteMock->expects($this->once())
            ->method('setIsActive')
            ->with(0);

        $customerMock = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $quoteMock->expects($this->once())
            ->method('assignCustomer')
            ->with($customerMock);

        $productDataObject = new \Magento\Framework\DataObject([
            'qty' => $productQty
        ]);
        $quoteMock->expects($this->once())
            ->method('addProduct')
            ->with($product, $productDataObject);
        $quoteMock->expects($this->once())
            ->method('collectTotals');
        $quoteMock->expects($this->once())
            ->method('save');

        $customerRepository->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\InitQuote(
            $context,
            $resultJsonFactory,
            $ebanxHelper,
            $storeManager,
            $product,
            $cartRepositoryInterface,
            $cartManagementInterface,
            $customerFactory,
            $customerRepository,
            $order,
            $session,
            $logger
        );

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }

    public function testExexuteConfigurableProduct()
    {
        $quoteId = 1;
        $customerId = 1;
        $baseSubtotal = 100;
        $subtotal = 100;
        $productId = 123;
        $productQty = 1;

        $rawJsonPost = json_encode([
            'product_id' => $productId,
            'product_qty' => $productQty,
            'super_attribute' => [
                ['attr_id' => 10, 'option_id' => 20],
                ['attr_id' => 20, 'option_id' => 30],
            ]
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
        $storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
        $cartRepositoryInterface = $this->getMockBuilder(\Magento\Quote\Api\CartRepositoryInterface::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $cartManagementInterface = $this->getMockBuilder(\Magento\Quote\Api\CartManagementInterface::class)
            ->setMethods(['createEmptyCart'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $customerFactory = $this->getMockBuilder(\Magento\Customer\Model\CustomerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $customerRepository = $this->getMockBuilder(\Magento\Customer\Api\CustomerRepositoryInterface::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $order = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $session = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->setMethods(['getCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(\DigitalHub\Ebanx\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn(new \Magento\Framework\DataObject([
                'content' => $rawJsonPost
            ]));

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $product->expects($this->once())
            ->method('load')
            ->with($productId)
            ->willReturn($product);

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getId','getBaseSubtotal','getSubtotal','setStore','setIsActive','assignCustomer','collectTotals','save','addProduct'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);

        $quoteMock->expects($this->once())
            ->method('getBaseSubtotal')
            ->willReturn($baseSubtotal);

        $quoteMock->expects($this->once())
            ->method('getSubtotal')
            ->willReturn($subtotal);

        $cartManagementInterface->expects($this->once())
            ->method('createEmptyCart')
            ->willReturn($quoteId);

        $cartRepositoryInterface->expects($this->once())
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);

        $session->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
                'cart_id' => $quoteId,
                'base_subtotal' => $baseSubtotal,
                'subtotal' => $subtotal
            ])
            ->willReturn($resultJson);

        $storeMock = $this->getMockBuilder(\Magento\Store\Model\Store::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $quoteMock->expects($this->once())
            ->method('setStore')
            ->with($storeMock);

        $quoteMock->expects($this->once())
            ->method('setIsActive')
            ->with(0);

        $customerMock = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $quoteMock->expects($this->once())
            ->method('assignCustomer')
            ->with($customerMock);

        $productDataObject = new \Magento\Framework\DataObject([
            'qty' => $productQty,
            'super_attribute' => [
                '10' => '20',
                '20' => '30'
            ]
        ]);
        $quoteMock->expects($this->once())
            ->method('addProduct')
            ->with($product, $productDataObject);
        $quoteMock->expects($this->once())
            ->method('collectTotals');
        $quoteMock->expects($this->once())
            ->method('save');

        $customerRepository->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\InitQuote(
            $context,
            $resultJsonFactory,
            $ebanxHelper,
            $storeManager,
            $product,
            $cartRepositoryInterface,
            $cartManagementInterface,
            $customerFactory,
            $customerRepository,
            $order,
            $session,
            $logger
        );

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
