<?php


namespace Ebanx\Payments\Controller\Payment;


use Ebanx\Payments\Helper\Data;
use Ebanx\Payments\Model\Resource\Order\Payment\Collection;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\OrderFactory;

class Update extends Action
{
    /**
     * @var Collection
     */
    protected $ebanxCollection;

    /**
     * @var Data
     */
    protected $ebanxHelper;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

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
     * @param Context      $context
     * @param Collection   $ebanxCollection
     * @param Data         $ebanxHelper
     * @param OrderFactory $orderFactory
     * @param JsonFactory  $jsonFactory
     */
    public function __construct(
        Context $context,
        Collection $ebanxCollection,
        Data $ebanxHelper,
        OrderFactory $orderFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->resultFactory     = $context->getResultFactory();
        $this->ebanxCollection   = $ebanxCollection;
        $this->ebanxHelper       = $ebanxHelper;
        $this->orderFactory      = $orderFactory;
        $this->jsonResultFactory = $jsonFactory;
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

        foreach ($hashCodes as $hashCode) {
            $orderId = $this->ebanxCollection->getOrderIdByPaymentHash($hashCode);
            if (!$orderId) {
                $result->setHttpResponseCode(400);
                $result->setData([
                    'status'  => 'ERROR',
                    'message' => 'Payment not found.',
                ]);

                return $result;
            }
            $data['order_id'] = $orderId;

            $order     = $this->orderFactory->create()->loadByIncrementId($orderId);
            $orderData = $order->getData();
            if (empty($orderData)) {
                $result->setHttpResponseCode(400);
                $result->setData([
                    'status'  => 'ERROR',
                    'message' => 'Order not found.',
                ]);

                return $result;
            }
            $data['order_data'] = $orderData;
        }
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

        return '';
    }
}
