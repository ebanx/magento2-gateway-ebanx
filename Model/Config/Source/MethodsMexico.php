<?php

namespace Ebanx\Payments\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MethodsMexico implements ArrayInterface
{
    const CREDIT_CARD = 'ebanx_cc_mx';
    const DEBIT_CARD = 'ebanx_dc_mx';
    const OXXO = 'ebanx_oxxo';

    public function toArray()
    {
        return [
            self::CREDIT_CARD => __('Credit Card'),
            self::DEBIT_CARD => __('Debit Card'),
            self::OXXO => __('OXXO')
        ];
    }

    final public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }
}