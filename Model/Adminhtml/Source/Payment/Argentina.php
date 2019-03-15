<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source\Payment;

class Argentina implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'creditcard',
                'label' => __('Credit Card')
            ],
        ];
    }
}
