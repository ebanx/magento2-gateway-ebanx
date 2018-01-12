<?php
namespace Ebanx\Payments\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Store\Model\StoreManagerInterface;

class RedirectUrl extends Action
{
    /**
     * @var Redirect
     */
    private $resultRedirect;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * RedirectUrl constructor.
     *
     * @param Context $context
     * @param Redirect $resultRedirect
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Redirect $resultRedirect,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->resultRedirect = $resultRedirect;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute() {
        return $this->resultRedirect->setUrl($this->storeManager->getStore()->getBaseUrl());
    }
}
