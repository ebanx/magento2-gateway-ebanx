<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxBalotoConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_baloto';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
