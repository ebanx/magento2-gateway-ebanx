<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxWebpayConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_webpay';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
