<?php

namespace Ebanx\Payments\Controller\Voucher;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Raw;

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
    /**
     * @var Raw
     */
    protected $_raw;

    public function __construct(
        Context $context,
        Data $helper,
        Raw $raw,
        Api $api
    ) {
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
        $this->_helper = $helper;
        $this->_gateway = $api->benjamin()->boleto();
        $this->_raw = $raw;
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
        return $this->_raw->setContents($this->_gateway->getTicketHTml($hash, $isSandbox));
    }
}
