<?php


namespace Ebanx\Payments\Controller\Payment;


use Ebanx\Payments\Model\Resource\Order\Payment\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Update extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $ebanxCollectionFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * Constructor
     *
     * @param Context           $context
     * @param CollectionFactory $ebanxCollectionFactory
     * @param JsonFactory       $jsonFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $ebanxCollectionFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->resultFactory          = $context->getResultFactory();
        $this->ebanxCollectionFactory = $ebanxCollectionFactory;
        $this->jsonResultFactory      = $jsonFactory;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result  = $this->jsonResultFactory->create();
        $request = $this->getRequest();
        $data    = $request->getParams();

        if ($errorMessage = $this->getErrorMessage()) {
            $result->setHttpResponseCode(400);
            $result->setData([
                'status'  => 'ERROR',
                'message' => $errorMessage,
            ]);

            return $result;
        }

        $hashCodes = explode(',', $request->getParam('hash_codes'));


        $result->setData($data);

        return $result;
    }

    private function getErrorMessage()
    {
        $request = $this->getRequest();
        if ($request->getParam('operation') !== 'payment_status_change') {
            return 'Invalid operation.';
        }

        if ($request->getParam('notification_type') !== 'update') {
            return 'Invalid notification type.';
        }

        if (empty($request->getParam('hash_codes'))) {
            return 'Invalid hash codes.';
        }
    }
}
