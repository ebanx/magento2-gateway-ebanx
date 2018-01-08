<?php
namespace Ebanx\Payments\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxSencillitoConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_sencillito';

    /**
     * @return array
     */
    public function getConfig()
    {
        return [];
    }
}
