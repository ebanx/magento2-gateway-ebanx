<?php
namespace Ebanx\Payments\Model\Resource\Order\Payment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ebanx\Payments\Model\Order\Payment as EbanxPayment;

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
        $this->_init('Ebanx\Payments\Model\Order\Payment', 'Ebanx\Payments\Model\Resource\Order\Payment');
    }
}
