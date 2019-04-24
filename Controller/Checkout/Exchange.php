<?php
namespace DigitalHub\Ebanx\Controller\Checkout;

class Exchange extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $priceCurrency;

	const EXCHANGE_ACTION = 'ws/exchange';

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Checkout\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \DigitalHub\Ebanx\Logger\Logger $logger,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Locale\CurrencyInterface $currency
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->_ebanxHelper = $ebanxHelper;
		$this->_session = $session;
		$this->_storeManager = $storeManager;
		$this->_logger = $logger;
        $this->curl = $curl;
        $this->_currency = $currency;
	}

	public function execute()
	{
		$installments = (int)$this->getRequest()->getParam('installments');
		$result = $this->resultJsonFactory->create();
        $base_total = $this->_session->getQuote()->getBaseGrandTotal();
        $country = $this->_session->getQuote()->getBillingAddress()->getCountryId();

		if($installments > 1){
			$base_total = $this->_ebanxHelper->calculateTotalWithInterest($base_total, $installments);
		}

        $integrationKey = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'live_integration_key');
        $sandboxIntegrationKey = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox_integration_key');
        $isSandbox = (int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox');

        $url = $isSandbox ? \Ebanx\Benjamin\Services\Http\Client::SANDBOX_URL . self::EXCHANGE_ACTION : \Ebanx\Benjamin\Services\Http\Client::LIVE_URL . self::EXCHANGE_ACTION;
        $integration_key = $isSandbox ? $sandboxIntegrationKey : $integrationKey;

        $merchant_currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $customer_currency = 'USD';

        switch($country){
            case 'BR':
                $customer_currency = 'BRL';
                break;
            case 'AR':
                $customer_currency = 'ARS';
                break;
            case 'CL':
                $customer_currency = 'CLP';
                break;
            case 'CO':
                $customer_currency = 'COP';
                break;
            case 'MX':
                $customer_currency = 'MXN';
                break;
            case 'PE':
                $customer_currency = 'PEN';
                break;
            case 'EC':
                $customer_currency = 'USD';
                break;
        }

        try {
            $this->curl->setHeaders([
                'integration_key' => $integration_key
            ]);

            $this->curl->post($url, [
                'integration_key' => $integration_key,
                'currency_code' => $merchant_currency,
                'currency_base_code' => $customer_currency,
            ]);

            $response = json_decode($this->curl->getBody());

            $total = $base_total * (float)$response->currency_rate->rate;
            $total_with_iof = $total + ($total * 0.0038);
            $total_formatted = $this->_currency->getCurrency($customer_currency)->toCurrency($total);
            $total_formatted_iof = $this->_currency->getCurrency($customer_currency)->toCurrency($total_with_iof);

            $result->setData([
                'success' => true,
                'total_formatted' => $total_formatted,
                'total_with_iof_formatted' => $customer_currency == 'BRL' ? $total_formatted_iof : null,
                'currency' => $customer_currency
            ]);
        } catch (\Exception $e) {
            $result->setData([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }

        return $result;
	}
}
