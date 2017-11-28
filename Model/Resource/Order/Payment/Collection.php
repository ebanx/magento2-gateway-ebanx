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

    /**
     * @param string $paymentHash
     *
     * @return int
     */
    public function getOrderIdByPaymentHash($paymentHash) {
        return (int) $this->addFilter('payment_hash', $paymentHash)->getLastItem()->getDataByKey('order_id');
    }

    /**
     * @param string $paymentHash
     *
     * @return string
     */
    public function getEnvironmentByPaymentHash($paymentHash) {
        return $this->addFilter('payment_hash', $paymentHash)->getLastItem()->getDataByKey('environment');
    }
}
