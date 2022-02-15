<?php

namespace DigitalHub\Ebanx\Model\Ui;

use DigitalHub\Ebanx\Helper\Data;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Payment\Gateway\Config\Config as GatewayConfig;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'digitalhub_ebanx_global';
    const BRAZILIAN_ISO_CODE = 'BR';

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var Data
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
     * @var bool
     */
    private $showBacenAlert = false;

    /**
     * ConfigProvider constructor.
     *
     * @param AssetRepository $assetRepository
     * @param GatewayConfig $gatewayConfig
     * @param StoreManagerInterface $storeManager
     * @param EncoderInterface $encoder
     * @param Data $ebanxHelper
     */
    public function __construct(
        AssetRepository $assetRepository,
        GatewayConfig $gatewayConfig,
        StoreManagerInterface $storeManager,
        EncoderInterface $encoder,
        Data $ebanxHelper,
        Information $storeInformation
    ) {
        $this->assetRepository = $assetRepository;
        $this->_ebanxHelper    = $ebanxHelper;
        $this->gatewayConfig   = $gatewayConfig;
        $this->storeId         = $storeManager->getStore()->getId();
        $this->encoder         = $encoder;
        $this->showBacenAlert  = $storeInformation->getStoreInformationObject(
                $storeManager->getStore()
            )->getCountryId() == self::BRAZILIAN_ISO_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $isSandbox              = $this->_ebanxHelper->getConfigData(
            'digitalhub_ebanx_global',
            'sandbox',
            $this->storeId
        );
        $isDebugEnabled         = $this->_ebanxHelper->getConfigData(
            'digitalhub_ebanx_global',
            'debug',
            $this->storeId
        );
        $public_integration_key = $isSandbox ?
            $this->_ebanxHelper->getConfigData(
                'digitalhub_ebanx_global',
                'sandbox_public_integration_key',
                $this->storeId
            ) :
            $this->_ebanxHelper->getConfigData(
                'digitalhub_ebanx_global',
                'live_public_integration_key',
                $this->storeId
            );

        return [
            'payment' => [
                'digitalhub_ebanx_global' => [
                    'ebanx_logo'             => $this->assetRepository->getUrl('DigitalHub_Ebanx::images/ebanx.png'),
                    'sandbox'                => (int)$isSandbox,
                    'public_integration_key' => $public_integration_key,
                    'debug'                  => (int)$isDebugEnabled,
                    'show_iof'               => (int)$this->_ebanxHelper->getConfigData(
                        'digitalhub_ebanx_global',
                        'show_iof',
                        $this->storeId
                    ),
                    'show_local_total'       => (int)$this->_ebanxHelper->getConfigData(
                        'digitalhub_ebanx_global',
                        'show_local_total',
                        $this->storeId
                    ),
                    'show_bacen_alert'       => $this->showBacenAlert,
                    'can_save_cc'            => (int)$this->_ebanxHelper->getConfigData(
                        'digitalhub_ebanx_global/cc',
                        'save',
                        $this->storeId
                    ),
                    'document_fields'        => [
                        'brazil_cpf'  => $this->_ebanxHelper->getConfigData(
                            'digitalhub_ebanx_global/customer_fields',
                            'document_field_brazil_cpf',
                            $this->storeId
                        ),
                        'brazil_cnpj' => $this->_ebanxHelper->getConfigData(
                            'digitalhub_ebanx_global/customer_fields',
                            'document_field_brazil_cpf',
                            $this->storeId
                        ),
                        'argentina'   => $this->_ebanxHelper->getConfigData(
                            'digitalhub_ebanx_global/customer_fields',
                            'document_field_argentina',
                            $this->storeId
                        ),
                        'colombia'    => $this->_ebanxHelper->getConfigData(
                            'digitalhub_ebanx_global/customer_fields',
                            'document_field_colombia',
                            $this->storeId
                        ),
                        'chile'       => $this->_ebanxHelper->getConfigData(
                            'digitalhub_ebanx_global/customer_fields',
                            'document_field_chile',
                            $this->storeId
                        ),
                    ],
                ],
            ],
        ];
    }
}
