<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source\Payment;

class Chile implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'servipag',
                'label' => __('Servipag')
            ],
            [
                'value' => 'sencillito',
                'label' => __('Sencillito')
            ],
            [
                'value' => 'webpay',
                'label' => __('Webpay')
            ],
            [
                'value' => 'multicaja',
                'label' => __('Multicaja')
            ],
        ];
    }
}
