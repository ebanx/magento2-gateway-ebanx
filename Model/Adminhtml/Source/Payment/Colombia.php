<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source\Payment;

class Colombia implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'eft',
                'label' => __('EFT')
            ],
            [
                'value' => 'baloto',
                'label' => __('Baloto')
            ],
            [
                'value' => 'creditcard',
                'label' => __('Credit Card')
            ],
        ];
    }
}
