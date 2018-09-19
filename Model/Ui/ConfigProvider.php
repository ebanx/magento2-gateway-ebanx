<?php

namespace DigitalHub\Ebanx\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use Magento\Framework\Json\EncoderInterface;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'digitalhub_ebanx_global';

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var \DigitalHub\Ebanx\Helper\Data
     */
    private $_ebanxHelper;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * ConfigProvider constructor.
     *
     * @param AssetRepository $assetRepository
     * @param GatewayConfig $gatewayConfig
     * @param StoreManagerInterface $storeManager
     * @param EncoderInterface $encoder
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     */
    public function __construct(
        AssetRepository $assetRepository,
        GatewayConfig $gatewayConfig,
        StoreManagerInterface $storeManager,
        EncoderInterface $encoder,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper
    ) {
        $this->assetRepository = $assetRepository;
        $this->_ebanxHelper = $ebanxHelper;
        $this->gatewayConfig = $gatewayConfig;
        $this->storeId = $storeManager->getStore()->getId();
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        // $this->gatewayConfig->setMethodCode(self::CODE);
        $isActive = (bool)$this->gatewayConfig->getValue('general/active', $this->storeId);
        $isSandbox = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox', $this->storeId);
        $isDebugEnabled = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'debug', $this->storeId);
        $public_integration_key = $isSandbox ?
            $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'sandbox_public_integration_key', $this->storeId) :
            $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'live_public_integration_key', $this->storeId);

        return [
            'payment' => [
                'digitalhub_ebanx_global' => [
                    'ebanx_logo' => $this->assetRepository->getUrl('DigitalHub_Ebanx::images/ebanx.png'),
                    'sandbox' => (int)$isSandbox,
                    'public_integration_key' => $public_integration_key,
                    'debug' => (int)$isDebugEnabled,
                    'show_iof' => (int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'show_iof', $this->storeId),
                    'show_local_total' => (int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'show_local_total', $this->storeId),
                    'can_save_cc' => (int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'save', $this->storeId),
                    'document_fields' => [
                        'brazil_cpf' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_brazil_cpf', $this->storeId),
                        'brazil_cnpj' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_brazil_cpf', $this->storeId),
                        'argentina' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_argentina', $this->storeId),
                        'colombia' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_colombia', $this->storeId),
                        'chile' => $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_chile', $this->storeId)
                    ]
                ]
            ]
        ];
    }
}
