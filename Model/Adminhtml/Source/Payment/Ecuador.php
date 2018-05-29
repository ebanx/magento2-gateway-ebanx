<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source\Payment;

class Ecuador implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'safetypay',
                'label' => __('SafetyPay')
            ]
        ];
    }
}
