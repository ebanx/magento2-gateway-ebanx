<?php
namespace DigitalHub\Ebanx\Model\ResourceModel\CreditCard\Token;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'ebanx_creditcard_token_collection';
	protected $_eventObject = 'token_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('DigitalHub\Ebanx\Model\CreditCard\Token', 'DigitalHub\Ebanx\Model\ResourceModel\CreditCard\Token');
	}

}
