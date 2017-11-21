<?php


namespace Ebanx\Payments\Controller\Payment;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Update extends Action
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * Constructor
     *
     * @param Context  $context
     */
    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        echo 'leme';
        die();
    }
}
