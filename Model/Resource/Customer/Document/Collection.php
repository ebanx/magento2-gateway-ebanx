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

    /**
     * @param string $customerId
     * @return string|null
     */
    public function getDocumentForCustomerId($customerId)
    {
        return $this->findByCustomerId($customerId)->getDocument();
    }

    /**
     * @param string $customerId
     * @return \Ebanx\Payments\Model\Customer\Document
     */
    public function findByCustomerId($customerId)
    {
        /**
         * @var $document \Ebanx\Payments\Model\Customer\Document
         */
        $document = $this->addFilter('customer_id', $customerId)->getLastItem();

        return $document;
    }
}
