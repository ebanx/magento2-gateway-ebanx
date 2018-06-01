<?php
namespace DigitalHub\Ebanx\Gateway\Config\Chile\Webpay;

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

        $chile_enabled_payments = explode(',',$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global', 'payments_chile', $storeId));
        if($ebanxActive && in_array('webpay', $chile_enabled_payments)){
            return true;
        }
        return false;
    }
}
