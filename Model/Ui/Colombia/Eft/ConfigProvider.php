<?php

namespace DigitalHub\Ebanx\Model\Ui\Colombia\Eft;

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
    const CODE = 'digitalhub_ebanx_colombia_eft';

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var \DigitalHub\Ebanx\Helper\Data
     */
    private $ebanxHelper;

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
        $this->gatewayConfig->setMethodCode(self::CODE);
        $isActiveGlobal = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'active', $this->storeId);

        return [
            'payment' => [
                'digitalhub_ebanx_colombia_eft' => [
                    'isActive' => (bool)($this->_ebanxHelper->getConfigData('digitalhub_ebanx_colombia_eft', 'active', $this->storeId) && $isActiveGlobal)
                ]
            ]
        ];
    }
}
