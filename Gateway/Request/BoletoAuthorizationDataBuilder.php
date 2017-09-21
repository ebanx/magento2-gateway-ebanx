<?php
namespace Ebanx\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Payment\Gateway\Helper\SubjectReader;

class BoletoAuthorizationDataBuilder implements BuilderInterface
{

    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;

    /**
     * CaptureDataBuilder constructor.
     *
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     */
    public function __construct(EbanxData $ebanxHelper)
    {
        $this->ebanxHelper = $ebanxHelper;
    }

    /**
     * @param array $buildSubject
     * @return mixed
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();
        $order = $paymentDataObject->getOrder();
        $storeId = $order->getStoreId();

        $request = [];

        $customerName = [
            'firstName' => $payment->getAdditionalInformation("firstname"),
            'lastName' => $payment->getAdditionalInformation("lastname"),
        ];
        $request['customerName'] = $customerName;
        $request['dueDateDays'] = (int) $this->ebanxHelper->getEbanxAbstractConfigData("due_date_days", $storeId);

        return $request;
    }
}