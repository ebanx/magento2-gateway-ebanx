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
        $objectManager = ObjectManager::getInstance();
        /** @var \Magento\Sales\Model\Order $fullOrder*/
        $fullOrder = $paymentDataObject->getPayment()->getOrder();
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer')
                                 ->setWebsiteId($order->getStoreId())
                                 ->load($order->getCustomerId());

        $taxVatNumber = $customer->getTaxvat() ?: $fullOrder->getBillingAddress()->getData('vat_id');

	    $person = new Person([
            'type' => Person::TYPE_PERSONAL,
            'document' => preg_replace('/[^0-9]/', '', $taxVatNumber),
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
