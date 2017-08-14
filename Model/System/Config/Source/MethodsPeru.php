<?php

namespace Ebanx\PaymentGateway\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MethodsPeru implements ArrayInterface
{
    const SAFETYPAY = 'ebanx_safetypay';
    const PAGOEFECTIVO = 'ebanx_pagoefectivo';

    public function toArray()
    {
        return [
            self::SAFETYPAY => __('SafetyPay'),
            self::PAGOEFECTIVO => __('PagoEfectivo')
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