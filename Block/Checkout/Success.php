<?php
namespace Ebanx\Payments\Block\Checkout;

use Ebanx\Payments\Helper\Data as Helper;
use Magento\Checkout\Model\Session;
use Magento\Framework\UrlInterface;
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
     * @var int
     */
    protected $_orderId;

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Success constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param Helper $helper
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Helper $helper,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_orderId = $checkoutSession->getLastRealOrderId();
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getDueDate() {
        return $this->_helper->getDueDate($this->_orderId, 'dd/MM');
    }

    /**
     * @return mixed
     */
    public function getVoucherUrl()
    {
        $hash = $this->_helper->getPaymentHash($this->_orderId);
        $isSandbox = $this->_helper->getPaymentMode($this->_orderId) === 'sandbox' ? true : false;
        return $this->_urlBuilder->getUrl('ebanx/voucher/show', array(
            'hash' => $hash,
            'is_sandbox' => $isSandbox
        ));
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->_orderId);
        }
        return $this->_order;
    }
}
