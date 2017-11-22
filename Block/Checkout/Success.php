<?php
namespace Ebanx\Payments\Block\Checkout;

use Ebanx\Payments\Gateway\Http\Client\Api;
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
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var \Ebanx\Benjamin\Services\Gateways\Boleto
     */
    protected $_gateway;

    /**
     * Success constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderFactory $orderFactory
     * @param Helper $helper
     * @param UrlInterface $urlBuilder
     * @param Api $api
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Helper $helper,
        UrlInterface $urlBuilder,
        Api $api,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
        $this->_gateway = $api->benjamin()->boleto();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getDueDate() {
        return $this->_helper->getDueDate($this->_checkoutSession->getLastRealOrderId(), 'dd/MM');
    }

    /**
     * @return mixed
     */
    public function getVoucherUrl()
    {
        $hash = $this->_helper->getPaymentHash($this->_checkoutSession->getLastRealOrderId());
        $isSandbox = $this->_helper->getPaymentMode($this->_checkoutSession->getLastRealOrderId()) === 'sandbox' ? true : false;
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
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());
        }
        return $this->_order;
    }
}
