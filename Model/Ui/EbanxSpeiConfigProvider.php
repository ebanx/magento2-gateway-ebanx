<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxSpeiConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_spei';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
