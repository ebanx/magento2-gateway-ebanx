<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source\Payment;

class Peru implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'pagoefectivo',
                'label' => __('PagoEfectivo')
            ],
            [
                'value' => 'safetypay',
                'label' => __('SafetyPay')
            ]
        ];
    }
}
