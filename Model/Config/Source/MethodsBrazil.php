<?php

namespace Ebanx\Payments\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class MethodsBrazil implements ArrayInterface
{
    const CREDIT_CARD = 'ebanx_cc_br';
    const BOLETO = 'ebanx_boleto';
    const TEF = 'ebanx_tef';
    const WALLET = 'ebanx_wallet';

    public function toArray()
    {
        return [
            self::CREDIT_CARD => __('Credit Card'),
            self::BOLETO => __('Boleto EBANX'),
            self::TEF => __('Online Banking (TEF)'),
            self::WALLET => __('EBANX Wallet')
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