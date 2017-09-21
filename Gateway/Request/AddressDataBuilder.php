<?php
namespace Ebanx\Payments\Gateway\Request;

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

        $result = [];

        $billingAddress = $order->getBillingAddress();
        if ($billingAddress) {

            $requestAddress = ["street" => $billingAddress->getStreetLine1(),
                "postalCode" => $billingAddress->getPostcode(),
                "city" => $billingAddress->getCity(),
                "state" => $billingAddress->getRegionCode(),
                "country" => $billingAddress->getCountryId()
            ];

            $result['address'] = $requestAddress;
        }

        return $result;
    }
}
