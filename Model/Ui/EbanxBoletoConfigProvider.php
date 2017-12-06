<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxBoletoConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_boleto';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
