<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class PlaceOrder extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
		\Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
		\Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
		\Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
		\Magento\Sales\Model\Order $order,
		\Magento\Checkout\Model\Session $session,
		\Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->cartRepositoryInterface = $cartRepositoryInterface;
		$this->cartManagementInterface = $cartManagementInterface;
		$this->paymentMethodManagement = $paymentMethodManagement;
		$this->order = $order;
		$this->session = $session;
		$this->addressRepository = $addressRepository;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$postData = json_decode(file_get_contents("php://input"));
		$cart_id = $postData->cart_id;
		$shipping_address_id = $postData->shipping_address_id;

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

		$address = $objectManager->create('Magento\Customer\Model\Address')->load($shipping_address_id);
		$addressData = $address->getData();

		$quote = $this->cartRepositoryInterface->get($cart_id);

		try {
			$billingAddress = $this->addressRepository->getById($address->getId());
			$quote->getBillingAddress()->importCustomerAddressData($billingAddress);
	        $quote->getShippingAddress()->addData($address->getData());
			$quote->collectTotals();
			$quote->save();

			$quote->getPayment()->importData([
				'method' => $postData->payment_method,
				'additional_data' => [
					'document_number' => '',
					'use_saved_cc' => $postData->token_id,
					'installments' => 1
				]
			]);

			$this->session->getQuote()->setIsActive(0); // inactivate old quote
			$this->session->getQuote()->save();

			$quote->setIsActive(1); // activate the new quote
	        $this->session->setQuoteId($quote->getId());
	        $this->session->replaceQuote($quote);

	        $orderId = $this->cartManagementInterface->placeOrder($quote->getId());
	        $order = $this->order->load($orderId);

	        $result->setData([
	            'success' => true,
				'order_increment_id' => $order->getIncrementId()
	        ]);
		} catch (\Exception $e){
			$result->setData([
				'error' => true,
				'message' => $e->getMessage()
			]);
		}

		return $result;
	}
}
