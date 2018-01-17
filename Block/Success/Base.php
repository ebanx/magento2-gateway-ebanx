<?php
namespace Ebanx\Payments\Block\Success;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Model\Resource\Order\Payment\Collection;
use Magento\Checkout\Model\Session;
use Magento\Directory\Model\Currency;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;

class Base extends Template
{
    /**
     * @var \Ebanx\Benjamin\Facade
     */
    protected $ebanx;

    /**
     * @var PriceCurrencyInterface
     */
    protected $currency;

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
     * @param Api $api
     * @param Currency $currency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        Collection $ebanxPaymentCollection,
        UrlInterface $urlBuilder,
        Api $api,
        Currency $currency,
        array $data = []
    ) {
        $this->_orderId                = $checkoutSession->getLastRealOrderId();
        $this->_orderFactory           = $orderFactory;
        $this->_ebanxPaymentCollection = $ebanxPaymentCollection;
        $this->_urlBuilder = $urlBuilder;
        $this->ebanx = $api->benjamin();
        $this->_urlBuilder             = $urlBuilder;
        $this->currency                = $currency;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSuccessPaymentBlock()
    {
        $paymentCode = $this->getPaymentCode();
        if ($this->isCash($paymentCode)) {
            return 'ebanx_cash';
        }
        if ($this->isRedirect($paymentCode)){
            return 'ebanx_redirect';
        }
        return $paymentCode;
    }

    public function formatAmount($currency, $amount)
    {
        $this->currency->load($currency);
        return $this->currency->format($amount);
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

    /**
     * @return string
     */
    public function getPaymentCode() {
        return $this->getOrder()->getPayment()->getMethodInstance()->getCode();
    }

    private function isCash($paymentCode)
    {
        try {
            $this->getChildChildHtml('ebanx_cash', $paymentCode);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function isRedirect($paymentCode)
    {
        try {
            $this->getChildChildHtml('ebanx_redirect', $paymentCode);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
