<?php
namespace DigitalHub\Ebanx\Block\Product\View;

use Magento\Customer\Model\Context;
use Magento\Sales\Model\Order;

class OneClickPayment extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        array $data = []
    ) {
        $this->_ebanxHelper = $ebanxHelper;
        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        if(!(int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cc', 'one_click_payment')){
            return '';
        }
        return parent::_toHtml();
    }
}
