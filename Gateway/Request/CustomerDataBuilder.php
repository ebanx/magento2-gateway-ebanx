<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Person;
use Ebanx\Payments\Observer\DocumentDataAssignObserver;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class CustomerDataBuilder
 */
class CustomerDataBuilder implements BuilderInterface
{
    private $customer;
    private $customerRepository;

    public function __construct(
        Customer $customer,
        CustomerRepository $customerRepository
    )
    {
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
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
        $payment = $paymentDataObject->getPayment();
        $billingAddress = $order->getBillingAddress();
        $document = $payment->getAdditionalInformation(DocumentDataAssignObserver::DOCUMENT);
        $document = preg_replace('/[^0-9]/', '', $document);
        $customer = $this->customer->setWebsiteId($order->getStoreId())
                                   ->loadByEmail($billingAddress->getEmail());
        $this->saveDocumentToCustomer($document, $customer);

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

    /**
     * @param $document
     * @param $customer
     */
    private function saveDocumentToCustomer($document, $customer)
    {
        $customer->setEbanxCustomerDocument($document);
    }
}
