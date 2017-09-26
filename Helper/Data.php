<?php

namespace Ebanx\Payments\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @desc Retrieve payment values from admin configuration
     * @param $field
     * @param $paymentMethodCode
     * @param $storeId
     * @return mixed
     */
    public function getConfigData($field, $paymentMethodCode, $storeId)
    {
        $path = 'payment/' . $paymentMethodCode . '/' . $field;

        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
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
     * Return the formatted currency. Adyen accepts the currency in multiple formats.
     * @param $amount
     * @return string
     */
    public function formatAmount($amount)
    {
        return (int)number_format($amount, 2, '', '');
    }
}
