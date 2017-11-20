<?php
namespace Ebanx\Payments\Block\Checkout;

use Ebanx\Payments\Helper\Data as Helper;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;

class Success extends Template
{

    /**
     * @var \Magento\Sales\Model\Order $order
     */
    protected $_order;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Success constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Helper $helper,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getDueDate() {
        return $this->_helper->getDueDate($this->_checkoutSession->getLastRealOrderId(), 'dd/MM');
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
        }
        return $this->_order;
    }
}
