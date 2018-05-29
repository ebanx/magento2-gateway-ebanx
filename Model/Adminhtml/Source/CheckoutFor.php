<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source;

class CheckoutFor implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'cpf',
                'label' => __('CPF - Individuals')
            ],
            [
                'value' => 'cnpj',
                'label' => __('CNPJ - Companies')
            ],
        ];
    }
}
