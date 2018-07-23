<?php
namespace DigitalHub\Ebanx\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Redirect extends Action
{
    private $checkoutSession;

    public function __construct(
        Context $context,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->resultFactory = $context->getResultFactory();
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->checkoutSession->getEbanxRedirectUrl());

        $this->checkoutSession->setEbanxRedirectUrl(null);

        return $resultRedirect;
    }
}
