<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxSafetyPayConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_safetypay';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
