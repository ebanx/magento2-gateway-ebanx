<?php
namespace Ebanx\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class CustomerDataBuilder
 */
class CustomerDataBuilder implements BuilderInterface
{

    /**
     * Add shopper data into request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $result = [];

        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);

        $order = $paymentDataObject->getOrder();
        $billingAddress = $order->getBillingAddress();
        $customerEmail = $billingAddress->getEmail();
        $customerId = $order->getCustomerId();

        if ($customerId > 0) {
            $result['customerId'] = $customerId;
        }

        $result ['customerEmail'] = $customerEmail;

        return $result;
    }
}
