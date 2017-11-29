<?php
namespace Ebanx\Payments\Block\Checkout;

use Ebanx\Payments\Model\Resource\Order\Payment\Collection;
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
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var \Ebanx\Payments\Model\Resource\Order\Payment\Collection
     */
    protected $_ebanxPaymentCollection;

    /**
     * Success constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param Collection $ebanxPaymentCollection
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Collection $ebanxPaymentCollection,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_orderId = $checkoutSession->getLastRealOrderId();
        $this->_orderFactory = $orderFactory;
        $this->_ebanxPaymentCollection = $ebanxPaymentCollection;
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSuccessPaymentBlock()
    {
        return $this->getOrder()->getPayment()->getMethodInstance()->getCode();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if ($this->_order === null) {
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->_orderId);
        }
        return $this->_order;
    }
}
