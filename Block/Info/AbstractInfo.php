<?php
namespace Ebanx\Payments\Block\Info;

use Ebanx\Payments\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Info;

class AbstractInfo extends Info
{
    /**
     * @var Data
     */
    protected $_ebanxHelper;

    /**
     * AbstractInfo constructor.
     *
     * @param Data $ebanxHelper
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Data $ebanxHelper,
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_ebanxHelper = $ebanxHelper;
    }

    /**
     * @return mixed
     */
    public function isSandboxMode()
    {
//        $storeId = $this->getInfo()->getOrder()->getStoreId();
        return true; //$this->_ebanxHelper->getEbanxAbstractConfigDataFlag('sandbox_mode', $storeId);
    }
}
