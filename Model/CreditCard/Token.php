<?php
namespace DigitalHub\Ebanx\Model\CreditCard;

class Token
    extends \Magento\Framework\Model\AbstractModel
    implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'ebanx_creditcard_token';
	protected $_cacheTag = 'ebanx_creditcard_token';
	protected $_eventPrefix = 'ebanx_creditcard_token';

	protected function _construct()
	{
		$this->_init('DigitalHub\Ebanx\Model\ResourceModel\CreditCard\Token');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];
		return $values;
	}
}
