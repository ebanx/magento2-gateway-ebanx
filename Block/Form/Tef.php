<?php
namespace Ebanx\Payments\Block\Form;

use Ebanx\Payments\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Form;

class Tef extends Form
{
    /**
     * @var string
     */
    protected $_template = 'Ebanx_Payments::form/tef.phtml';

    /**
     * @var Data
     */
    protected $_ebanxHelper;

    /**
     * Boleto constructor.
     *
     * @param Context $context
     * @param Data $ebanxHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $ebanxHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_ebanxHelper = $ebanxHelper;
        var_dump($this->_template);exit;
    }
}
