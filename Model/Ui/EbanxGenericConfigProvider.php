<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxGenericConfigProvider implements ConfigProviderInterface
{
    const CODE = 'ebanx_abstract';

    public function getConfig()
    {
        return [
            'payment' => []
        ];
    }
}
