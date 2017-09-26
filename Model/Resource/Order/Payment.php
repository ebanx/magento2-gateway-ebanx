<?php
namespace Ebanx\Payments\Model\Resource\Order;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Payment extends AbstractDb
{
    /**
     * Construct
     */
    public function _construct()
    {
        $this->_init('ebanx_order_payment', 'entity_id');
    }
}