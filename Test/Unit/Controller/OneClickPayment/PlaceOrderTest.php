<?php
namespace DigitalHub\Ebanx\Test\Unit\Controller\OneClickPayment;

class PlaceOrderTest extends \PHPUnit\Framework\TestCase
{
    public function testExecute()
    {
        $quoteId = 1;
        $shippingAddressId = 1;
        $addressData = [];
        $paymentMethod = 'digitalhub_ebanx_brazil_creditcard';
        $tokenId = 1;

        $rawJsonPost = json_encode([
            'cart_id' => $quoteId,
            'shipping_address_id' => $shippingAddressId,
            'payment_method' => $paymentMethod,
            'token_id' => $tokenId,
        ]);

        $orderId = 123;
        $orderIncrementId = '000000123';

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
		$cartManagementInterface = $this->getMockBuilder(\Magento\Quote\Api\CartManagementInterface::class)
            ->setMethods(['placeOrder'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
		$paymentMethodManagement = $this->getMockBuilder(\Magento\Quote\Api\PaymentMethodManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
		$order = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
		$session = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->setMethods(['setQuoteId','replaceQuote','getQuote'])
            ->disableOriginalConstructor()
            ->getMock();
		$addressRepository = $this->getMockBuilder(\Magento\Customer\Api\AddressRepositoryInterface::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $quotePaymentMock = $this->getMockBuilder(\Magento\Quote\Model\Quote\Payment::class)
            ->setMethods(['importData'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getBillingAddress','getShippingAddress','collectTotals', 'save', 'getPayment', 'setIsActive', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteBillingAddress = $this->getMockBuilder(\Magento\Customer\Api\Data\AddressInterface::class)
            ->setMethods(['importCustomerAddressData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $quoteShippingAddress = $this->getMockBuilder(\Magento\Customer\Api\Data\AddressInterface::class)
            ->setMethods(['importCustomerAddressData','setCollectShippingRates','collectShippingRates','getShippingRatesCollection'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $addressRepository->expects($this->any())
            ->method('getById')
            ->with($shippingAddressId)
            ->willReturn($quoteShippingAddress);

        $quoteMock->expects($this->any())
            ->method('getId')
            ->willReturn($quoteId);

        $quoteMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($quoteBillingAddress);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($quoteShippingAddress);

        $quoteShippingAddress->expects($this->once())
            ->method('importCustomerAddressData')
            ->with($quoteShippingAddress);

        $quoteBillingAddress->expects($this->once())
            ->method('importCustomerAddressData')
            ->with($quoteShippingAddress); // shipping and billing are the same

        $cartRepositoryInterface->expects($this->once())
            ->method('get')
            ->willReturn($quoteMock);

        $quoteMock->expects($this->once())
            ->method('collectTotals');

        $quoteMock->expects($this->once())
            ->method('save');

        $quoteMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($quotePaymentMock);

        $quoteMock->expects($this->once())
            ->method('setIsActive')
            ->with(1);

        $quotePaymentMock->expects($this->once())
            ->method('importData')
            ->with([
				'method' => $paymentMethod,
				'additional_data' => [
					'document_number' => '',
					'use_saved_cc' => $tokenId,
					'installments' => 1
				]
			]);

        $sessionQuoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['setIsActive','save'])
            ->disableOriginalConstructor()
            ->getMock();

        $sessionQuoteMock->expects($this->once())
            ->method('setIsActive')
            ->with(0);

        $sessionQuoteMock->expects($this->once())
            ->method('save');

        $session->expects($this->any())
            ->method('getQuote')
            ->willReturn($sessionQuoteMock);

        $session->expects($this->any())
            ->method('setQuoteId')
            ->with($quoteId);

        $session->expects($this->any())
            ->method('replaceQuote')
            ->with($quoteMock);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn(new \Magento\Framework\DataObject([
                'content' => $rawJsonPost
            ]));

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $cartManagementInterface->expects($this->once())
            ->method('placeOrder')
            ->with($quoteId)
            ->will($this->throwException(new \Exception('Error Message')));

        $orderMock = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->setMethods(['getIncrementId'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
	            'error' => true,
				'message' => 'Error Message'
	        ]);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\PlaceOrder(
            $context,
            $resultJsonFactory,
            $ebanxHelper,
            $cartRepositoryInterface,
            $cartManagementInterface,
            $paymentMethodManagement,
            $order,
            $session,
            $addressRepository
        );

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }

    public function testExecuteWithError()
    {
        $quoteId = 1;
        $shippingAddressId = 1;
        $addressData = [];
        $paymentMethod = 'digitalhub_ebanx_brazil_creditcard';
        $tokenId = 1;

        $rawJsonPost = json_encode([
            'cart_id' => $quoteId,
            'shipping_address_id' => $shippingAddressId,
            'payment_method' => $paymentMethod,
            'token_id' => $tokenId,
        ]);

        $orderId = 123;
        $orderIncrementId = '000000123';

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
		$cartManagementInterface = $this->getMockBuilder(\Magento\Quote\Api\CartManagementInterface::class)
            ->setMethods(['placeOrder'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
		$paymentMethodManagement = $this->getMockBuilder(\Magento\Quote\Api\PaymentMethodManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
		$order = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
		$session = $this->getMockBuilder(\Magento\Checkout\Model\Session::class)
            ->setMethods(['setQuoteId','replaceQuote','getQuote'])
            ->disableOriginalConstructor()
            ->getMock();
		$addressRepository = $this->getMockBuilder(\Magento\Customer\Api\AddressRepositoryInterface::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $quotePaymentMock = $this->getMockBuilder(\Magento\Quote\Model\Quote\Payment::class)
            ->setMethods(['importData'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getBillingAddress','getShippingAddress','collectTotals', 'save', 'getPayment', 'setIsActive', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteBillingAddress = $this->getMockBuilder(\Magento\Customer\Api\Data\AddressInterface::class)
            ->setMethods(['importCustomerAddressData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $quoteShippingAddress = $this->getMockBuilder(\Magento\Customer\Api\Data\AddressInterface::class)
            ->setMethods(['importCustomerAddressData','setCollectShippingRates','collectShippingRates','getShippingRatesCollection'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $addressRepository->expects($this->any())
            ->method('getById')
            ->with($shippingAddressId)
            ->willReturn($quoteShippingAddress);

        $quoteMock->expects($this->any())
            ->method('getId')
            ->willReturn($quoteId);

        $quoteMock->expects($this->once())
            ->method('getBillingAddress')
            ->willReturn($quoteBillingAddress);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($quoteShippingAddress);

        $quoteShippingAddress->expects($this->once())
            ->method('importCustomerAddressData')
            ->with($quoteShippingAddress);

        $quoteBillingAddress->expects($this->once())
            ->method('importCustomerAddressData')
            ->with($quoteShippingAddress); // shipping and billing are the same

        $cartRepositoryInterface->expects($this->once())
            ->method('get')
            ->willReturn($quoteMock);

        $quoteMock->expects($this->once())
            ->method('collectTotals');

        $quoteMock->expects($this->once())
            ->method('save');

        $quoteMock->expects($this->once())
            ->method('getPayment')
            ->willReturn($quotePaymentMock);

        $quoteMock->expects($this->once())
            ->method('setIsActive')
            ->with(1);

        $quotePaymentMock->expects($this->once())
            ->method('importData')
            ->with([
				'method' => $paymentMethod,
				'additional_data' => [
					'document_number' => '',
					'use_saved_cc' => $tokenId,
					'installments' => 1
				]
			]);

        $sessionQuoteMock = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['setIsActive','save'])
            ->disableOriginalConstructor()
            ->getMock();

        $sessionQuoteMock->expects($this->once())
            ->method('setIsActive')
            ->with(0);

        $sessionQuoteMock->expects($this->once())
            ->method('save');

        $session->expects($this->any())
            ->method('getQuote')
            ->willReturn($sessionQuoteMock);

        $session->expects($this->any())
            ->method('setQuoteId')
            ->with($quoteId);

        $session->expects($this->any())
            ->method('replaceQuote')
            ->with($quoteMock);

        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn(new \Magento\Framework\DataObject([
                'content' => $rawJsonPost
            ]));

        $resultJson = $this->getMockBuilder(\Magento\Framework\Controller\Result\Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $resultJsonFactory->expects($this->once())
            ->method('create')
            ->willReturn($resultJson);

        $cartManagementInterface->expects($this->once())
            ->method('placeOrder')
            ->with($quoteId)
            ->willReturn($orderId);

        $orderMock = $this->getMockBuilder(\Magento\Sales\Model\Order::class)
            ->setMethods(['getIncrementId'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderMock->expects($this->once())
            ->method('getIncrementId')
            ->willReturn($orderIncrementId);

        $order->expects($this->once())
            ->method('load')
            ->with($orderId)
            ->willReturn($orderMock);

        $resultJson->expects($this->once())
            ->method('setData')
            ->with([
	            'success' => true,
				'order_increment_id' => $orderIncrementId
	        ]);

        $controller = new \DigitalHub\Ebanx\Controller\OneClickPayment\PlaceOrder(
            $context,
            $resultJsonFactory,
            $ebanxHelper,
            $cartRepositoryInterface,
            $cartManagementInterface,
            $paymentMethodManagement,
            $order,
            $session,
            $addressRepository
        );

        $this->assertEquals(
            $resultJson,
            $controller->execute()
        );
    }
}
