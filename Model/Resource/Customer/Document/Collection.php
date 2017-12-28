<?php

namespace Ebanx\Payments\Model\Resource\Customer\Document;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Billing agreements resource collection
 */
class Collection extends AbstractCollection
{
    /**
     * Collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ebanx\Payments\Model\Customer\Document', 'Ebanx\Payments\Model\Resource\Customer\Document');
    }

    public function findByCustomerId($customerId)
    {
        return $this->addFilter('customer_id', $customerId)->getLastItem();
    }
}
