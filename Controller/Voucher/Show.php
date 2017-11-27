<?php

namespace Ebanx\Payments\Controller\Voucher;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory;

class Show extends Action
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
     * @var \Ebanx\Benjamin\Services\Gateways\Boleto
     */
    protected $_gateway;
    protected $_rawFactory;

    public function __construct(
        Context $context,
        Data $helper,
        RawFactory $rawFactory,
        Api $api
    ) {
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
        $this->_helper = $helper;
        $this->_gateway = $api->benjamin()->boleto();
        $this->_rawFactory = $rawFactory;
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
        return $this->_rawFactory->create()->setContents($this->_gateway->getTicketHTml($hash, $isSandbox));
    }
}
