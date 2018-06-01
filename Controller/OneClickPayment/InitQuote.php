<?php
namespace DigitalHub\Ebanx\Controller\OneClickPayment;

class InitQuote extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order $order,
        \Magento\Customer\Model\Session $session,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;

        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->order = $order;
        $this->session = $session;
        $this->_logger = $logger;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

        $quote = $this->_initQuote();
        $quote_id = $quote->getId();

        return $result->setData([
            'cart_id' => $quote_id,
            'base_subtotal' => $quote->getBaseSubtotal(),
            'subtotal' => $quote->getSubtotal()
        ]);
	}

    private function _initQuote()
    {
        $store = $this->_storeManager->getStore();
        $cartId = $this->cartManagementInterface->createEmptyCart(); //Create empty cart
        $quote = $this->cartRepositoryInterface->get($cartId); // load empty cart quote
		$quote->setIsActive(0);
        $quote->setStore($store);

        $customer= $this->customerRepository->getById($this->session->getCustomerId());
        $quote->assignCustomer($customer);

        $postData = json_decode($this->getRequest()->getContent());

        //add items in quote
        $product = $this->_product->load((int)$postData->product_id);
		$params = [
			'qty' => (int)$postData->product_qty
		];

		if(isset($postData->super_attribute) && $postData->super_attribute){
			$super_attribute = [];
			foreach($postData->super_attribute as $item){
				$super_attribute[$item->attr_id] = $item->option_id;
			}
			$params['super_attribute'] = $super_attribute;
		}

		$dataObject = new \Magento\Framework\DataObject();
		$dataObject->setData($params);

        $quote->addProduct($product, $dataObject);
        $quote->collectTotals();
        $quote->save();

        return $quote;
    }
}
