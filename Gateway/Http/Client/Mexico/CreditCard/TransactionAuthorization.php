<?php
namespace DigitalHub\Ebanx\Gateway\Http\Client\Mexico\CreditCard;

use DigitalHub\Ebanx\Gateway\Http\Util\HttpUtil;
use Magento\Payment\Gateway\Http\ClientInterface;

/**
 * Class TransactionSale
 */
class TransactionAuthorization implements ClientInterface
{

    protected $_ebanxHelper;
    protected $_logger;
    protected $_ebanxClient;
    protected $_appState;
    protected $_storeManager;

    /**
     * PaymentRequest constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \DigitalHub\Ebanx\Logger\Logger $logger
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \DigitalHub\Ebanx\Logger\Logger $logger,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        $this->_ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;
        $this->_appState = $context->getAppState();
        $this->_storeManager = $storeManager;

        // initialize client
        $config = new \Ebanx\Benjamin\Models\Configs\Config([
            'integrationKey' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'live_integration_key'),
            'sandboxIntegrationKey' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox_integration_key'),
            'isSandbox' => (int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox'),
            'baseCurrency' => $this->_storeManager->getStore()->getBaseCurrencyCode(),
        ]);

        $creditCardConfig = new \Ebanx\Benjamin\Models\Configs\CreditCardConfig([
            'maxInstalments' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'max_installments'),
            'minInstalmentAmount' => $this->_ebanxHelper->getMinInstallmentValue('MX')
        ]);

        $this->_ebanxClient = HttpUtil::setupEbanxClient($config, $creditCardConfig);

        $this->_logger->info('Client Authorization :: __construct');
    }

    /**
     * @param \Magento\Payment\Gateway\Http\TransferInterface $transferObject
     * @return mixed
     * @throws ClientException
     */
    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();

        $this->_logger->info('Client Authorization :: placeRequest');

        $response = [];

        try {
            $ebanxPayment = new \Ebanx\Benjamin\Models\Payment($request);
            $payment_result = $this->_ebanxClient->create($ebanxPayment);
            $response['payment_result'] = $payment_result;

            $this->_logger->info('EBANX Result Authorization :: placeRequest', [$payment_result]);

        } catch (\Exception $e){
            $response['error'] = $e->getMessage();

            $this->_logger->info(sprintf('EBANX Exception `%s` :: `%s`', get_class($e), __METHOD__), [$e->getMessage()]);
        }

        return $response;
    }
}
