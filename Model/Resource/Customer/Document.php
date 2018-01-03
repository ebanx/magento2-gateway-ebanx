<?php
namespace Ebanx\Payments\Model\Resource\Customer;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Document extends AbstractDb
{
    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init('ebanx_customer_document', 'id');
    }
}
