<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class CreditCardAuthorizationDataBuilder implements BuilderInterface
{
    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;

    /**
     * CaptureDataBuilder constructor.
     *
     * @param EbanxData $ebanxHelper
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
        $order = $paymentDataObject->getOrder();
        $storeId = $order->getStoreId();

        // TODO: read auto capture flag

        return [
            'type' => 'creditcard',
            // TODO: read token and cvv
        ];
    }
}
