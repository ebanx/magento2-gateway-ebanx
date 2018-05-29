<?php
namespace DigitalHub\Ebanx\Block\CreditCard;

class Saved extends \Magento\Framework\View\Element\Template
{
	private $_customerSession;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\Session $session
	)
	{
		$this->_customerSession = $session;
		parent::__construct($context);
	}

	public function getItems()
	{
		if($this->_customerSession->getCustomerId()){
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

			$collection = $objectManager->create('DigitalHub\Ebanx\Model\CreditCard\Token')->getCollection();
			$collection->addFieldToFilter('customer_id', $this->_customerSession->getCustomerId());

			return $collection;
		}
		return [];
	}
}
