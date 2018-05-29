<?php
namespace DigitalHub\Ebanx\Gateway\Config\Chile\Servipag;

class ActiveValueHandler implements \Magento\Payment\Gateway\Config\ValueHandlerInterface
{
    private $_ebanxHelper;

    public function __construct
    (
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Checkout\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_session = $session;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
    }

    public function handle(array $subject, $storeId = null)
    {
        $ebanxActive = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'active', $storeId);
        $currency = $this->_storeManager->getStore()->getBaseCurrency()->getCode();

        $chile_enabled_payments = explode(',',$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'payments_chile', $storeId));
        if($ebanxActive && in_array('servipag', $chile_enabled_payments)){
            return true;
        }
        return false;
    }
}
