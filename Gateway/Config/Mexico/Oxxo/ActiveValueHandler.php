<?php
namespace DigitalHub\Ebanx\Gateway\Config\Mexico\Oxxo;

class ActiveValueHandler implements \Magento\Payment\Gateway\Config\ValueHandlerInterface
{
    private $_ebanxHelper;

    public function __construct
    (
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Checkout\Model\Session $session,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_session = $session;
        $this->_logger = $logger;
    }

    public function handle(array $subject, $storeId = null)
    {
        $ebanxActive = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'active', $storeId);

        $mexico_enabled_payments = explode(',',$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'payments_mexico', $storeId));
        if($ebanxActive && in_array('oxxo', $mexico_enabled_payments)){
            return true;
        }
        return false;
    }
}
