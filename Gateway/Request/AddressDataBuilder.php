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

        // TODO: Add Address Fields Mapping
        $street = $this->_ebanxHelper->getAddressData('street', $billingAddress);
        $streetNumber = $this->_ebanxHelper->getAddressData('street_number', $billingAddress);
        $streetComplement = $this->_ebanxHelper->getAddressData('complement', $billingAddress);

        // TODO: The Magento default billing form don't display region field for Argentina, Chile, Colombia and Mexico
        $region = $billingAddress->getRegionCode();
        if(!$region && $billingAddress->getCountryId() == 'AR'){
            $region = "--";
        }
        if(!$region && $billingAddress->getCountryId() == 'CL'){
            $region = "--";
        }
        if(!$region && $billingAddress->getCountryId() == 'CO'){
            $region = "--";
        }
        if(!$region && $billingAddress->getCountryId() == 'MX'){
            $region = "--";
        }

        $address = new \Ebanx\Benjamin\Models\Address([
            'address' => $street ? $street : 'N/A',
            'streetNumber' => $streetNumber ? $streetNumber : 'N/A',
            'city' => $billingAddress->getCity(),
            'country' => \Ebanx\Benjamin\Models\Country::fromIso($billingAddress->getCountryId()),
            'state' => $region,
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
