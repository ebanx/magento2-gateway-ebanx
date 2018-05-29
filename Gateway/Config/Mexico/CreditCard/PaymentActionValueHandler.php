<?php
namespace DigitalHub\Ebanx\Gateway\Config\Mexico\CreditCard;

class PaymentActionValueHandler implements \Magento\Payment\Gateway\Config\ValueHandlerInterface
{
    private $_ebanxHelper;

    public function __construct
    (
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
    }

    public function handle(array $subject, $storeId = null)
    {
        $autoCapture = $this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'capture', $storeId);
        if((bool)$autoCapture){
            return 'authorize_capture';
        }
        return 'authorize';
    }
}
