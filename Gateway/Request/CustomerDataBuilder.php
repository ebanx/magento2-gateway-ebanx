<?php
namespace DigitalHub\Ebanx\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use DigitalHub\Ebanx\Observer\Brazil\CreditCard\DataAssignObserver;

/**
 * Class CustomerDataBuilder
 */
class CustomerDataBuilder implements BuilderInterface
{

    private $_ebanxHelper;
    private $_logger;
    private $_session;

    /**
     * CustomerDataBuilder constructor.
     *
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param \Magento\Checkout\Model\Session $session
     * @param \DigitalHub\Ebanx\Helper\Data $logger
     */
    public function __construct(
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Checkout\Model\Session $session,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_session = $session;
        $this->_logger = $logger;
        $this->_logger->info('CustomerDataBuilder :: __construct');
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
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);

        $order = $paymentDataObject->getOrder();
        $payment = $paymentDataObject->getPayment();
        $billingAddress = $order->getBillingAddress();
        $countryId = $billingAddress->getCountryId();
        $additionalData = $payment->getAdditionalInformation();

        $documentNumber = false;
        if(in_array($countryId, ['BR','CL','AR','CO'])){
            $documentNumberField = $this->_ebanxHelper->getCustomerDocumentNumberField($this->_session->getQuote());
            $documentNumber = $this->_ebanxHelper->getCustomerDocumentNumber($this->_session->getQuote(), $documentNumberField);

            if(!$documentNumber && isset($additionalData[DataAssignObserver::DOCUMENT_NUMBER])){
                $documentNumber = $additionalData[DataAssignObserver::DOCUMENT_NUMBER];
            }
        }

        $person = new \Ebanx\Benjamin\Models\Person([
            'type' => $this->_ebanxHelper->getPersonType($documentNumber, $countryId),
            'document' => $documentNumber,
            'email' => $billingAddress->getEmail(),
            'name' => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
            'phoneNumber' => $billingAddress->getTelephone(),
            'ip' => $order->getRemoteIp(),
        ]);

        $request = [
            'person' => $person,
            'responsible' => $person,
        ];

        $this->_logger->info('CustomerDataBuilder :: documentNumber', [$documentNumber]);
        $this->_logger->info('CustomerDataBuilder :: build', $request);

        return $request;
    }
}
