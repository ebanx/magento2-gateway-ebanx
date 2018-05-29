<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;

class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize')
            ]
        ];
    }
}
