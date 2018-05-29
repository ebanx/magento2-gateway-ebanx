<?php
namespace DigitalHub\Ebanx\Controller\Eft;

class Banklist extends \Magento\Framework\App\Action\Action
{
	protected $resultJsonFactory;
	protected $ebanxHelper;
	protected $priceCurrency;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Framework\HTTP\Client\Curl $curl
    )
	{
        parent::__construct($context);
		$this->resultJsonFactory = $resultJsonFactory;
		$this->ebanxHelper = $ebanxHelper;
        $this->curl = $curl;
	}

	public function execute()
	{
		$result = $this->resultJsonFactory->create();

        $integrationKey = $this->ebanxHelper->getConfigData('digitalhub_ebanx_global', 'live_integration_key');
        $sandboxIntegrationKey = $this->ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox_integration_key');
        $isSandbox = (int)$this->ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox');

        $url = $isSandbox ? 'https://sandbox.ebanx.com/ws/getBankList' : 'https://api.ebanx.com/ws/getBankList';
        $integration_key = $isSandbox ? $sandboxIntegrationKey : $integrationKey;

        $items = [
            ['label' => '', 'value' => '']
        ];

        try {
            $this->curl->setHeaders([
                'integration_key' => $integration_key
            ]);

            $this->curl->post($url, [
                'integration_key' => $integration_key,
                'operation' => 'request',
                'country' => 'co'
            ]);

            $response = json_decode($this->curl->getBody());

            foreach($response as $bank){
                $items[] = [
                    'label' => $bank->name,
                    'value' => $bank->code
                ];
            }
        } catch (\Exception $e) { }

        return $result->setData([
            'items' => $items
        ]);
	}
}
