<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxPagoEfectivoConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_pagoefectivo';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
