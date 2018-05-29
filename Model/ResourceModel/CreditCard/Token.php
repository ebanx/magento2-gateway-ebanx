<?php
namespace DigitalHub\Ebanx\Model\ResourceModel\CreditCard;

class Token extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}

	protected function _construct()
	{
		$this->_init('ebanx_creditcard_token', 'id');
	}
}
