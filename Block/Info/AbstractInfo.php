<?php
namespace DigitalHub\Ebanx\Block\Info;

use Magento\Framework\View\Element\Template;

class AbstractInfo extends \Magento\Payment\Block\Info
{
    protected $_ebanxHelper;

    /**
     * @var \Adyen\Payment\Model\ResourceModel\Order\Payment\CollectionFactory
     */
    protected $_adyenOrderPaymentCollectionFactory;

    /**
     * AbstractInfo constructor.
     *
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_ebanxHelper = $ebanxHelper;
    }
}
