<?php
namespace DigitalHub\Ebanx\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use DigitalHub\Ebanx\Observer\Brazil\CreditCard\DataAssignObserver;

/**
 * Class AddressDataBuilder
 */
class AddressDataBuilder implements BuilderInterface
{

    private $_ebanxHelper;
    private $_logger;

    /**
     * AddressDataBuilder constructor.
     *
     * @param \Magento\Checkout\Model\Session $session
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param \DigitalHub\Ebanx\Helper\Data $logger
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_session = $session;
        $this->_ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;
        $this->_logger->info('AddressDataBuilder :: __construct');
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
        $billingAddress = $this->_session->getQuote()->getBillingAddress();

        $additionalData = $payment->getAdditionalInformation();

        $street = $this->_ebanxHelper->getAddressData('street', $billingAddress);
        $streetNumber = $this->_ebanxHelper->getAddressData('street_number', $billingAddress);
        $streetComplement = $this->_ebanxHelper->getAddressData('complement', $billingAddress);

        $address = new \Ebanx\Benjamin\Models\Address([
            'address' => $street ? $street : $this->_ebanxHelper->getFullAddressData($billingAddress),
            'streetNumber' => $streetNumber ? $streetNumber : 'N/A',
            'city' => $billingAddress->getCity(),
            'country' => \Ebanx\Benjamin\Models\Country::fromIso($billingAddress->getCountryId()),
            'state' => $billingAddress->getRegionCode() ? $billingAddress->getRegionCode() : 'N/A',
            'streetComplement' => $streetComplement ? $streetComplement : '',
            'zipcode' => $billingAddress->getPostcode(),
        ]);

        $request = [
            'address' => $address
        ];

        $this->_logger->info('AddressDataBuilder :: build', $request);

        return $request;
    }
}
