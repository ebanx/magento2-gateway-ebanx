<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxCreditCardConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_creditcard';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
