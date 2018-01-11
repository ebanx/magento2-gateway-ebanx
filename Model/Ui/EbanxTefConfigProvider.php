<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxTefConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ebanx_tef';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
