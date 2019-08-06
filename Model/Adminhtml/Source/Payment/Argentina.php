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
            [
                'value' => 'rapipago',
                'label' => __('Rapipago')
            ],
            [
                'value' => 'pagofacil',
                'label' => __('PagoFacil')
            ],
            [
                'value' => 'cupondepagos',
                'label' => __('Cupon de Pagos')
            ],
        ];
    }
}
