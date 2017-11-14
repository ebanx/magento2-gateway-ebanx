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
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);

        $order = $paymentDataObject->getOrder();
        var_dump($paymentDataObject->getPayment()->getOrder()->getId());
        $billingAddress = $order->getBillingAddress();
	    $person = new Person([
            'type' => Person::TYPE_PERSONAL,
            'document' => '85351346893',
            'email' => $billingAddress->getEmail(),
            'name' => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
            'phoneNumber' => $billingAddress->getTelephone(),
            'ip' => $order->getRemoteIp(),
        ]);

        return [
            'person' => $person,
            'responsible' => $person,
        ];
    }
}
