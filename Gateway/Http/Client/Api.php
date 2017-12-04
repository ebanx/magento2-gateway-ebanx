<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Ebanx\Payments\Helper\Data as Helper;
use Ebanx\Benjamin\Models\Configs\Config;
use Magento\Store\Model\StoreManagerInterface;

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
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * Api constructor.
     *
     * @param Helper $helper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Helper $helper,
        StoreManagerInterface $storeManager
    )
    {
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->benjamin = EBANX($this->getConfig());
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
            'baseCurrency' => $this->_storeManager->getStore()->getBaseCurrencyCode(),
            'notificationUrl' => '', // TODO: create notification controller
            'redirectUrl' => '', // TODO: create notification controller
            'userValues' => array(
                1 => 'from_magento2',
                3 => 'version=0.1.0', //TODO: Create a method to get the current version
            ),
        ));
    }
}
