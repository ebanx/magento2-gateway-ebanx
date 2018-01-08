<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxOxxoConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_oxxo';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
