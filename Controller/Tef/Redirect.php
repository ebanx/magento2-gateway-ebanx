<?php

namespace Ebanx\Payments\Controller\Tef;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect as resultRedirect;

class Redirect extends Action
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;
    /**
     * @var Data
     */
    protected $_helper;
    /**
<<<<<<< HEAD
     * @var \Ebanx\Benjamin\Services\Gateways\Tef
     */
    protected $_ebanx;
    /**
     * @var resultRedirect
     */
    protected $_resultRedirect;

    /**
     * Redirect constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param resultRedirect $resultRedirect
     * @param Api $api
     */
    public function __construct(
        Context $context,
        Data $helper,
        resultRedirect $resultRedirect,
        Api $api
    ) {
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
        $this->_helper = $helper;
        $this->_ebanx = $api->benjamin();
        $this->_resultRedirect = $resultRedirect;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute() {
        $hash = $this->getRequest()->getParam('hash');
        $isSandbox = $this->getRequest()->getParam('is_sandbox');
        $paymentInfo = $this->_ebanx->paymentInfo()->findByHash($hash, $isSandbox);
        $this->getResponse()->setRedirect($paymentInfo['payment']['redirect_url']);
        return $this->getResponse();
    }
}
