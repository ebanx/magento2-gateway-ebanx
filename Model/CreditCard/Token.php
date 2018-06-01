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

    public function getTokenByIdAndCustomer($token_id, $customer_id)
    {
        $tokenCollection = $this->getCollection();
        $tokenCollection->addFieldToFilter('id', (int)$token_id);
        $tokenCollection->addFieldToFilter('customer_id', (int)$customer_id);
        $tokenCollection->load();
        return $tokenCollection->getSize() ? $tokenCollection->getFirstItem() : null;
    }

    public function customerHasToken($customer_id)
    {
        $tokenCollection = $this->getCollection();
        $tokenCollection->addFieldToFilter('customer_id', (int)$customer_id);
        $tokenCollection->load();
        return $tokenCollection->getSize() ? true : false;
    }
}
