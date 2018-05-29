<?php
namespace DigitalHub\Ebanx\Controller\CreditCard\Checkout;

class Saved extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $_customerSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Customer\Model\Session $session
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
		$this->_customerSession = $session;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

        $items = [];

        if($this->_customerSession->getCustomerId()){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$collection = $objectManager->create('DigitalHub\Ebanx\Model\CreditCard\Token')->getCollection();
			$collection->addFieldToFilter('customer_id', $this->_customerSession->getCustomerId());
			$collection->addFieldToFilter('payment_method', $this->getRequest()->getParam('method'));
			foreach($collection as $item){
                $items[] = [
                    'id' => $item->getId(),
                    'masked_card_number' => $item->getMaskedCardNumber(),
                    'payment_type_code' => $item->getPaymentTypeCode(),
                    'payment_method' => $item->getPaymentMethod(),
                ];
            }
		}

        return $result->setData([
			'items' => $items
        ]);
	}
}
