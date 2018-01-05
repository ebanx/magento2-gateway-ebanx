<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxWalletConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_wallet';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
