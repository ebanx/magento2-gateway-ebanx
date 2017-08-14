<?php

namespace Ebanx\PaymentGateway\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MethodsColombia implements ArrayInterface
{
    const EFT = 'ebanx_pse';
    const BALOTO = 'ebanx_baloto';

    public function toArray()
    {
        return [
            self::EFT => __('PSE - Pago Seguros en Líne (EFT)'),
            self::BALOTO => __('Baloto')
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