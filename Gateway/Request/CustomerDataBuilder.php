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

	    $person = new Person([
            'type' => Person::TYPE_PERSONAL,
            'document' => '85351346893', //TODO: Get document number
            'email' => $billingAddress->getEmail(),
            'name' => 'Name', //TODO: Get name
            'phoneNumber' => '123456789', //TODO: Get phone number
//            'ip' => $data->getRemoteIp(),
        ]);

        return [
            'person' => $person,
            'responsible' => $person,
        ];
    }
}
