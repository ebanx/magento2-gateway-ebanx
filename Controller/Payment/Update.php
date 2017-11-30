<?php

namespace Ebanx\Payments\Controller\Payment;

use Ebanx\Payments\Gateway\Http\Client\Api;
use Ebanx\Payments\Helper\Data;
use Ebanx\Payments\Model\Resource\Order\Payment\Collection;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

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
     * @var Order
     */
    protected $order;

    /**
     * @var OrderResource $orderResource
     */
    protected $orderResource;

    /**
     * @var Json\Interceptor
     */
    protected $jsonInterceptor;

    /**
     * Constructor
     *
     * @param Context          $context
     * @param Data             $ebanxHelper
     * @param Api              $ebanxApi
     * @param Collection       $ebanxCollection
     * @param Order            $order
     * @param OrderResource    $orderResource
     * @param Json\Interceptor $jsonInterceptor
     */
    public function __construct(
        Context $context,
        Data $ebanxHelper,
        Api $ebanxApi,
        Collection $ebanxCollection,
        Order $order,
        OrderResource $orderResource,
        Json\Interceptor $jsonInterceptor
    ) {
        parent::__construct($context);
        $this->ebanxHelper     = $ebanxHelper;
        $this->ebanxApi        = $ebanxApi;
        $this->ebanxCollection = $ebanxCollection;
        $this->order           = $order;
        $this->orderResource   = $orderResource;
        $this->jsonInterceptor = $jsonInterceptor;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result  = $this->jsonInterceptor;
        $request = $this->getRequest();

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

            $order     = $this->order->loadByIncrementId($orderId);
            $orderData = $order->getData();
            if (empty($orderData)) {
                $result->setHttpResponseCode(400);
                $result->setData([
                    'status'  => 'ERROR',
                    'message' => 'Order not found.',
                ]);

                return $result;
            }

            $ebanxPaymentStatus = $this->getEbanxPaymentStatus($hashCode);
            if (!$ebanxPaymentStatus) {
                $result->setHttpResponseCode(400);
                $result->setData([
                    'status'  => 'ERROR',
                    'message' => 'Payment not found on EBANX.',
                ]);

                return $result;
            }

            $ebanxToMagentoStatus = $this->getEbanxToMagentoStatus($ebanxPaymentStatus);
            if ($order->getStatus() === $ebanxToMagentoStatus) {
                $result->setData([
                    'status' => 'SUCCESS',
                ]);

                return $result;
            }

            $order->setStatus($ebanxToMagentoStatus);
            $order->addStatusHistoryComment('EBANX: The payment has been updated to: ' . $ebanxToMagentoStatus);
            $this->orderResource->save($order);
        }
        $result->setData([
            'status' => 'SUCCESS',
        ]);

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
        $isSandbox   = $this->ebanxCollection->getEnvironmentByPaymentHash($hash) === 'sandbox';
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
