<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Ebanx\Benjamin\Models\Currency;
use Ebanx\Payments\Helper\Data as Helper;
use Ebanx\Benjamin\Models\Configs\Config;

class Api
{
    /**
     * @var \Ebanx\Benjamin\Facade
     */
    private $benjamin;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * Api constructor.
     *
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->benjamin = EBANX($this->getConfig());
        $this->helper = $helper;
    }

    /**
     * @return \Ebanx\Benjamin\Facade
     */
    public function benjamin()
    {
        return $this->benjamin;
    }

    /**
     * @return Config
     */
    private function getConfig()
    {
        return new Config(array(
            'integrationKey' => $this->helper->getEbanxAbstractConfigData('integration_key_live'),
            'sandboxIntegrationKey' => $this->helper->getEbanxAbstractConfigData('integration_key_sandbox'),
            'isSandbox' => $this->helper->getEbanxAbstractConfigData('mode'),
            'baseCurrency' => Currency::USD, // TODO: get store currency
            'notificationUrl' => '', // TODO: create notification controller
            'redirectUrl' => '', // TODO: create notification controller
            'userValues' => array(
                1 => 'from_magento2',
                3 => 'version=1.0.0', //TODO: Create a method to get the current version
            ),
        ));
    }
}
