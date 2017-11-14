<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Person;
use Magento\Customer\Model\Customer;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class CustomerDataBuilder
 */
class CustomerDataBuilder implements BuilderInterface
{
    private $customer;

    public function __construct(Customer $customer = null)
    {
        $this->customer = $customer;
    }

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
        /** @var \Magento\Sales\Model\Order $fullOrder*/
        $fullOrder = $paymentDataObject->getPayment()->getOrder();
        /** @var \Magento\Customer\Model\Data\Customer $customer */
        $customer = $this->customer->setWebsiteId($order->getStoreId())
                                   ->loadByEmail($billingAddress->getEmail());

        $document = $customer->getTaxvat() ?: $fullOrder->getBillingAddress()->getData('vat_id');
        preg_replace('/[^0-9]/', '', $document);

	    $person = new Person([
            'type' => $this->getPersonType($document, $billingAddress->getCountryId()),
            'document' => $document,
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

    public function getPersonType($document, $countryAbbr)
    {
        if ($countryAbbr !== 'BR' || strlen($document) < 14) {
            return Person::TYPE_PERSONAL;
        }

        return Person::TYPE_BUSINESS;
    }
}
