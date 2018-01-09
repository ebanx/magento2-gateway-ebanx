<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxServipagConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_servipag';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
