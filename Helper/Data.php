<?php

namespace Ebanx\Payments\Helper;

use Ebanx\Payments\Model\Resource\Order\Payment;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Ebanx\Payments\Model\Order\Payment as EbanxPaymentModel;
use Ebanx\Payments\Model\Resource\Order\Payment as EbanxResourceModel;
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
    public function __construct(
        Context $context,
        EbanxPaymentModel $ebanxPaymentModel,
        EbanxResourceModel $ebanxResourceModel,
        Payment\CollectionFactory $ebanxData
    )
    {
        $this->_ebanxPaymentModel = $ebanxPaymentModel;
        $this->_ebanxResourceModel = $ebanxResourceModel;
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

    public function getDueDate($orderId, $format = 'YYYY-MM-dd HH:mm:ss') {
        $date = $this->_ebanxData->create()->addFilter('order_id', $orderId)->getLastItem()->getDataByKey('due_date');
        $dueDate = new Zend_Date($date);
        return $dueDate->get($format);
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
}
