<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Country;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class AddressDataBuilder
 */
class AddressDataBuilder implements BuilderInterface
{
    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;

    /**
     * AddressDataBuilder constructor.
     *
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     */
    public function __construct(EbanxData $ebanxHelper)
    {
        $this->ebanxHelper = $ebanxHelper;
    }

    /**
     * Create address info request
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

        return [
            'address' => new Address([
                'address' => $billingAddress->getStreetLine1(),
                'streetNumber' => 'N/A', // TODO: get street number
                'city' => $billingAddress->getCity(),
                'country' => Country::fromIso($billingAddress->getCountryId()),
                'state' => $billingAddress->getRegionCode(),
                'streetComplement' => $billingAddress->getStreetLine2(),
                'zipcode' => $billingAddress->getPostcode(),
            ])
        ];
    }
}
