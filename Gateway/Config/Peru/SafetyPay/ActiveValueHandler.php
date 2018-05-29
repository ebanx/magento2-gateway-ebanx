<?php
namespace DigitalHub\Ebanx\Gateway\Config\Peru\SafetyPay;

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

        $peru_enabled_payments = explode(',',$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'payments_peru', $storeId));
        if($ebanxActive && in_array('safetypay', $peru_enabled_payments)){
            return true;
        }
        return false;
    }
}
