<?php

namespace Ebanx\Payments\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PersonType implements ArrayInterface
{
    const CPF = "cpf";
    const CNPJ = "cnpj";

    public function toArray()
    {
        return [
            self::CPF => __('CPF - Individuals'),
            self::CNPJ => __('CNPJ - Companies')
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