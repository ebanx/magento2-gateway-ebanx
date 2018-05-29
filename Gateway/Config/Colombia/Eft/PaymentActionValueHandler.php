<?php
namespace DigitalHub\Ebanx\Gateway\Config\Colombia\Eft;

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
        return 'authorize';
    }
}
