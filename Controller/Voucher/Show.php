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
     * @var \Ebanx\Benjamin\Facade
     */
    protected $ebanx;
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
        $this->ebanx = $api->benjamin();
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

        // TODO: Create a method getTicketHTml() on \Ebanx\Benjamin\Facade
        $paymentTypeCode = $this->ebanx->paymentInfo()->findByHash($hash)['payment']['payment_type_code'];

        return $this->_raw->setContents(
            $this->ebanx
                ->{$paymentTypeCode}()
                ->getTicketHTml($hash, $isSandbox)
        );
    }
}
