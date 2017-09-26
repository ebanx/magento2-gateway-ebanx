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
     * @param integer $paymentId
     * @return array
     */
    public function getTotalAmount($paymentId)
    {
        $connection = $this->getConnection();

        $sumCond = new \Zend_Db_Expr("SUM(ebanx_order_payment.{$connection->quoteIdentifier(EbanxPayment::AMOUNT)})");

        $select = $connection->select()->from(
            ['ebanx_order_payment' => $this->getTable('ebanx_order_payment')],
            ['total_amount' => $sumCond]
        )->where(
            'payment_id = :payment_id'
        );

        return $connection->fetchAll($select, [':payment_id' => $paymentId]);
    }

    /**
     * @param string $paymentId
     * @return $this
     */
    public function addPaymentFilterAscending($paymentId)
    {
        $this->addFieldToFilter('payment_id', $paymentId);
        $this->getSelect()->order(['created_at ASC']);
        return $this;
    }

    /**
     * @param string $paymentId
     * @return $this
     */
    public function addPaymentFilterDescending($paymentId)
    {
        $this->addFieldToFilter('payment_id', $paymentId);
        $this->getSelect()->order(['created_at DESC']);
        return $this;
    }
}
