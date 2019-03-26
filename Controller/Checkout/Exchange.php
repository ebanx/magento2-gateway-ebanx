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

        $base_currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $to_currency = 'USD';

        switch($country){
            case 'BR':
                $to_currency = 'BRL';
                break;
            case 'AR':
                $to_currency = 'ARS';
                break;
            case 'CL':
                $to_currency = 'CLP';
                break;
            case 'CO':
                $to_currency = 'COP';
                break;
            case 'MX':
                $to_currency = 'MXN';
                break;
            case 'PE':
                $to_currency = 'PEN';
                break;
            case 'EC':
                $to_currency = 'USD';
                break;
        }

        try {
            $this->curl->setHeaders([
                'integration_key' => $integration_key
            ]);

            $this->curl->post($url, [
                'integration_key' => $integration_key,
                'currency_code' => $to_currency,
                'currency_base_code' => $base_currency
            ]);

            $response = json_decode($this->curl->getBody());

            $total = $base_total * (float)$response->currency_rate->rate;
            $total_with_iof = $total + ($total * 0.0038);
            $total_formatted = $this->_currency->getCurrency($to_currency)->toCurrency($total);
            $total_formatted_iof = $this->_currency->getCurrency($to_currency)->toCurrency($total_with_iof);

            $result->setData([
                'success' => true,
                'total_formatted' => $total_formatted,
                'total_with_iof_formatted' => $to_currency == 'BRL' ? $total_formatted_iof : null,
                'currency' => $to_currency
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
