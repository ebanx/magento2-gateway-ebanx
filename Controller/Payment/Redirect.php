<?php
namespace DigitalHub\Ebanx\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;

class Redirect extends Action
{
    private $checkoutSession;

    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $order = $this->checkoutSession->getLastRealOrder();

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->checkoutSession->getEbanxRedirectUrl());

        $this->checkoutSession->setEbanxRedirectUrl(null);

        return $resultRedirect;
    }
}
