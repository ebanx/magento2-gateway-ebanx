<?php
namespace Ebanx\Payments\Model\Ui;

use Ebanx\Payments\Helper\Data;
use Magento\Checkout\Model\ConfigProviderInterface;

class EbanxCreditCardConfigProvider implements ConfigProviderInterface
{

    const CODE = 'ebanx_creditcard';
    private $ebanxHelper;

    public function __construct(Data $ebanxHelper) {
        $this->ebanxHelper = $ebanxHelper;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $mode = $this->ebanxHelper->getEbanxAbstractConfigData('mode') ? 'sandbox' : 'live';

        return [
            'payment' => [
                'ebanx' => [
                    'publicKey' => $this->ebanxHelper->getEbanxAbstractConfigData('integration_key_public_' . $mode),
                    'debug' => $mode
                ]
            ]
        ];
    }
}
