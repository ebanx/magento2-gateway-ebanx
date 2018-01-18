<?php
namespace Ebanx\Payments\Gateway\Http\Client;

use Ebanx\Benjamin\Models\Configs\CreditCardConfig;
use Ebanx\Payments\Helper\Data as Helper;
use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Country;
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
        $this->benjamin = EBANX($this->getConfig(), $this->getCreditCardConfig());
    }

    /**
     * @return \Ebanx\Benjamin\Facade
     */
    public function benjamin()
    {
        return $this->benjamin;
    }

    /**
     * @param  string  $gatewayName Gateway accessor method name in benjamin's facade
     * @param  string  $countryCode ISO-3166 two letter code
     * @return boolean
     */
    public function isAvailableForCountry($gatewayName, $countryCode)
    {
        if (!$gatewayName) {
            return true;
        }

        $gateway = $this->benjamin->{$gatewayName}();
        $country = Country::fromIso($countryCode);

        return $gateway->isAvailableForCountry($country);
    }

    /**
     * @return Config
     */
    private function getConfig()
    {
        return new Config([
            'integrationKey' => $this->helper->getEbanxAbstractConfigData('integration_key_live'),
            'sandboxIntegrationKey' => $this->helper->getEbanxAbstractConfigData('integration_key_sandbox'),
            'isSandbox' => $this->helper->getEbanxAbstractConfigData('mode'),
            'baseCurrency' => $this->_storeManager->getStore()->getBaseCurrencyCode(),
            'notificationUrl' => '', // TODO: create notification controller
            'redirectUrl' => $this->_storeManager->getStore()->getBaseUrl(),
            'userValues' => [
                1 => 'from_magento2',
                3 => 'version=1.0.0', //TODO: Create a method to get the current version
            ],
        ]);
    }

    /**
     * @return CreditCardConfig
     */
    private function getCreditCardConfig()
    {
        $creditCardConfig = new CreditCardConfig([
            'maxInstalments' => $this->helper->getEbanxAbstractConfigData('max_instalments'),
            'minInstalmentAmount' => $this->helper->getEbanxAbstractConfigData('min_instalment_value'),
        ]);

        $rates = json_decode($this->helper->getEbanxAbstractConfigData('interest_rates'), true);

        usort($rates, function ($value, $previous) {
            if ($value['installments'] === $previous['installments']) {
                return 0;
            }

            return ($value['installments'] < $previous['installments']) ? -1 : 1;
        });

        for ($i = 1; $i <= $this->helper->getEbanxAbstractConfigData('max_instalments'); $i++) {
            foreach ($rates as $key => $interestConfig) {
                if ($i <= $interestConfig['installments']) {
                    $creditCardConfig->addInterest($i, $interestConfig['interest_rate']);
                    break;
                }
            }
        }

        return $creditCardConfig;
    }
}
