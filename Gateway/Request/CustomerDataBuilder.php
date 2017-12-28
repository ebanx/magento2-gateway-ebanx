<?php

namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Person;
use Ebanx\Payments\Model\Customer\Document;
use Ebanx\Payments\Model\Resource\Customer\Document as DocumentResource;
use Ebanx\Payments\Model\Resource\Customer\Document\Collection as DocumentCollection;
use Ebanx\Payments\Observer\DocumentDataAssignObserver;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Interceptor;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class CustomerDataBuilder
 */
class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Interceptor
     */
    private $customerResourceModelInterceptor;

    /**
     * @var DocumentResource
     */
    private $ebanxDocumentResource;

    /**
     * @var DocumentCollection
     */
    private $ebanxDocumentCollection;

    /**
     * CustomerDataBuilder constructor.
     *
     * @param Customer           $customer
     * @param Interceptor        $customerResourceModelInterceptor
     * @param DocumentResource   $ebanxDocumentResource
     * @param DocumentCollection $ebanxDocumentCollection
     */
    public function __construct(
        Customer $customer,
        Interceptor $customerResourceModelInterceptor,
        DocumentResource $ebanxDocumentResource,
        DocumentCollection $ebanxDocumentCollection
    ) {
        $this->customer                         = $customer;
        $this->customerResourceModelInterceptor = $customerResourceModelInterceptor;
        $this->ebanxDocumentResource            = $ebanxDocumentResource;
        $this->ebanxDocumentCollection          = $ebanxDocumentCollection;
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
        $this->saveDocumentForCustomerId($document, $customer->getId());

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
     * @param string $document
     * @param string $customerId
     */
    private function saveDocumentForCustomerId($document, $customerId)
    {
        if (!$customerId || !$document) {
            return;
        }

        /**
         * @var Document $documentModel
         */
        $documentModel = $this->ebanxDocumentCollection
            ->findByCustomerId($customerId);

        if ($documentModel->getCustomerId() !== $customerId) {
            $documentModel->setCustomerId($customerId);
        }
        $documentModel->setDocument($document);

        $this->ebanxDocumentResource->save($documentModel);
    }
}
