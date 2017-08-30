<?php

namespace Ebanx\PaymentGateway\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MethodsChile implements ArrayInterface
{
    const SENCILLITO = 'ebanx_sencillito';
    const SERVIPAG = 'ebanx_servipag';

    public function toArray()
    {
        return [
            self::SENCILLITO => __('Sencillito'),
            self::SERVIPAG => __('Servipag')
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