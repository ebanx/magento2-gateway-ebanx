<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxMulticajaConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_multicaja';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
