<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Person;
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

        $person = new Person([
            'type' => Person::TYPE_PERSONAL,
            'document' => '00000000000',
            'email' => $customerEmail,
//            'ip' => $data->getRemoteIp(),
//            'name' => $person->getCustomerFirstname() . ' ' . $person->getCustomerLastname(),
//            'phoneNumber' => $data->getBillingAddress()->getTelephone(),
        ]);

        return [
            'person' => $person,
            'responsible' => $person,
        ];
    }
}
