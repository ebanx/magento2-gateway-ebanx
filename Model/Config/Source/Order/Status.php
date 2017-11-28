<?php
namespace Ebanx\Payments\Model\Config\Source\Order;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'foo',
                'label' => __('Foo'),
            ],
            [
                'value' => 'bar',
                'label' => __('Bar'),
            ],
        ];
    }
}
