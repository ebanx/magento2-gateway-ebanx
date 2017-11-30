<?php
namespace Ebanx\Payments\Controller\Payment;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Helper\Data;
use Ebanx\Payments\Model\Resource\Order\Payment\Collection;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\OrderFactory;

class Update extends Action
{
    /**
     * @var Api
     */
    protected $ebanxApi;

    /**
     * @var Data
     */
    protected $ebanxHelper;

    /**
     * @var Collection
     */
    protected $ebanxCollection;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * Constructor
     *
     * @param Context      $context
     * @param Data         $ebanxHelper
     * @param Api          $ebanxApi
     * @param Collection   $ebanxCollection
     * @param OrderFactory $orderFactory
     * @param JsonFactory  $jsonFactory
     */
    public function __construct(
        Context $context,
        Data $ebanxHelper,
        Api $ebanxApi,
        Collection $ebanxCollection,
        OrderFactory $orderFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->ebanxHelper = $ebanxHelper;
        $this->ebanxApi = $ebanxApi;
        $this->ebanxCollection   = $ebanxCollection;
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

            $ebanxPaymentStatus = $this->getEbanxPaymentStatus($hashCode);
            if (!$ebanxPaymentStatus) {
                $result->setHttpResponseCode(400);
                $result->setData([
                    'status'  => 'ERROR',
                    'message' => 'Payment not found on EBANX.',
                ]);

                return $result;
            }
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

    /**
     * @param string $hash
     * @return string
     */
    private function getEbanxPaymentStatus($hash)
    {
        $isSandbox = $this->ebanxCollection->getEnvironmentByPaymentHash($hash) === 'sandbox';
        $paymentInfo = $this->ebanxApi->benjamin()->paymentInfo()->findByHash($hash, $isSandbox);

        if ($paymentInfo['status'] !== 'SUCCESS') {
            return '';
        }

        return $paymentInfo['payment']['status'];
    }

    private function getEbanxToMagentoStatus($ebanxStatus)
    {
        $status = [
            'CO' => $this->ebanxHelper->getEbanxAbstractConfigData('payment_co_status'),
            'PE' => $this->ebanxHelper->getEbanxAbstractConfigData('payment_pe_status'),
            'OP' => $this->ebanxHelper->getEbanxAbstractConfigData('payment_op_status'),
            'CA' => $this->ebanxHelper->getEbanxAbstractConfigData('payment_ca_status'),
        ];

        return $status[strtoupper($ebanxStatus)];
    }
}
