<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source\Payment;

class Mexico implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'creditcard',
                'label' => __('Credit/Debit Card')
            ],
            [
                'value' => 'oxxo',
                'label' => __('OXXO')
            ],
            [
                'value' => 'spei',
                'label' => __('SPEI')
            ]
        ];
    }
}
