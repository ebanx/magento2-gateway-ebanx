<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Person;
use Magento\Framework\App\ObjectManager;
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
        $billingAddress = $order->getBillingAddress();
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = ObjectManager::getInstance()
                                 ->create('Magento\Customer\Model\Customer')
                                 ->setWebsiteId($order->getStoreId())
                                 ->loadByEmail($billingAddress->getEmail());

	    $person = new Person([
            'type' => Person::TYPE_PERSONAL,
            'document' => $customer->getTaxvat(),
            'email' => $billingAddress->getEmail(),
            'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'phoneNumber' => $billingAddress->getTelephone(),
            'ip' => $order->getRemoteIp(),
        ]);

        return [
            'person' => $person,
            'responsible' => $person,
        ];
    }
}
