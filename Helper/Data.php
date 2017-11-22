<?php

namespace Ebanx\Payments\Helper;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Model\Resource\Order\Payment;
use Ebanx\Payments\Model\Resource\Order\Payment\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Ebanx\Payments\Model\Order\Payment as EbanxPaymentModel;
use Ebanx\Payments\Model\Resource\Order\Payment as EbanxResourceModel;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Date;

class Data extends AbstractHelper
{
    /**
     * @var EbanxPaymentModel
     */
    protected $_ebanxPaymentModel;
    /**
     * @var EbanxResourceModel
     */
    protected $_ebanxResourceModel;
    /**
     * @var Payment
     */
    protected $_ebanxData;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param CollectionFactory $ebanxData
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CollectionFactory $ebanxData,
        StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
        $this->_ebanxData = $ebanxData;
        parent::__construct($context);
    }

    /**
     * @desc Returns EBANX configuration values
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getEbanxAbstractConfigData($field, $storeId = null)
    {
        return $this->getConfigData($field, 'ebanx_abstract', $storeId);
    }

    /**
     * @param $orderId
     * @param string $format
     *
     * @return string
     */
    public function getDueDate($orderId, $format = 'YYYY-MM-dd HH:mm:ss') {
        $date = $this->_ebanxData->create()->addFilter('order_id', $orderId)->getLastItem()->getDataByKey('due_date');
        $dueDate = new Zend_Date($date);
        return $dueDate->get($format);
    }

    /**
     * @param $orderId
     *
     * @return mixed
     */
    public function getBarCode($orderId) {
        return $this->_ebanxData->create()->addFilter('order_id', $orderId)->getLastItem()->getDataByKey('bar_code');
    }

    /**
     * @param $orderId
     *
     * @return mixed
     */
    public function getPaymentHash($orderId) {
        return $this->_ebanxData->create()->addFilter('order_id', $orderId)->getLastItem()->getDataByKey('payment_hash');
    }

    /**
     * @param $orderId
     *
     * @return mixed
     */
    public function getPaymentMode($orderId) {
        return $this->_ebanxData->create()->addFilter('order_id', $orderId)->getLastItem()->getDataByKey('environment');
    }

    /**
     * Return the formatted currency.
     * @param $amount
     * @return string
     */
    public function formatAmount($amount)
    {
        return (int)number_format($amount, 2, '', '');
    }

    /**
     * @desc Retrieve payment values from admin configuration
     * @param $field
     * @param $paymentMethodCode
     * @param $storeId
     * @return mixed
     */
    private function getConfigData($field, $paymentMethodCode, $storeId)
    {
        $path = 'payment/' . $paymentMethodCode . '/' . $field;

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $hash
     *
     * @return array
     */
    public function getPaymentByHash($hash)
    {
        return (new Api($this, $this->_storeManager))->benjamin()->paymentInfo()->findByHash($hash);
    }
}
