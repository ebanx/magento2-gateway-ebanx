<?php
namespace Ebanx\Payments\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Ebanx\Payments\Helper\Data as EbanxData;
use Ebanx\Payments\Model\Resource\Order\Payment\CollectionFactory;

/**
 * Class CustomerDataBuilder
 */
class RefundDataBuilder implements BuilderInterface
{

    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;

    /**
     * @var \Ebanx\Payments\Model\Resource\Order\Payment\CollectionFactory
     */
    private $orderPaymentCollectionFactory;

    /**
     * RefundDataBuilder constructor.
     * @param \Ebanx\Payments\Helper\Data $ebanxHelper
     * @param \Ebanx\Payments\Model\Resource\Order\Payment\CollectionFactory $orderPaymentCollectionFactory
     */
    public function __construct(
        EbanxData $ebanxHelper,
        CollectionFactory $orderPaymentCollectionFactory
    )
    {
        $this->ebanxHelper = $ebanxHelper;
        $this->orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $amount =  SubjectReader::readAmount($buildSubject);

        $order = $paymentDataObject->getOrder();
        $payment = $paymentDataObject->getPayment();
        $token = $payment->getCcTransId();
        $currency = $payment->getOrder()->getOrderCurrencyCode();
        $grandTotal = $payment->getOrder()->getGrandTotal();


        // check if it contains a split payment
        $orderPaymentCollection = $this->orderPaymentCollectionFactory
            ->create()
            ->addFieldToFilter('payment_id', $payment->getId());

        // partial refund if multiple payments check refund strategy
        if ($orderPaymentCollection->getSize() > 1) {

            $refundStrategy = $this->ebanxHelper->getEbanxAbstractConfigData(
                'split_payments_refund_strategy',
                $order->getStoreId()
            );
            $ratio = null;

            if ($refundStrategy == "1") {
                // Refund in ascending order
                $orderPaymentCollection->addPaymentFilterAscending($payment->getId());
            } elseif ($refundStrategy == "2") {
                // Refund in descending order
                $orderPaymentCollection->addPaymentFilterDescending($payment->getId());
            } elseif ($refundStrategy == "3") {
                // refund based on ratio
                $ratio =  $amount / $grandTotal;
                $orderPaymentCollection->addPaymentFilterAscending($payment->getId());
            }

            // loop over payment methods and refund them all
            $result = [];
            foreach ($orderPaymentCollection as $splitPayment) {
                // could be that not all the split payments need a refund
                if ($amount > 0) {
                    if ($ratio) {
                        // refund based on ratio calculate refund amount
                        $modificationAmount = $ratio * (
                                $splitPayment->getAmount() - $splitPayment->getTotalRefunded()
                            );
                    } else {
                        // total authorised amount of the split payment
                        $splitPaymentAmount = $splitPayment->getAmount() - $splitPayment->getTotalRefunded();

                        // if rest amount is zero go to next payment
                        if (!$splitPaymentAmount > 0) {
                            continue;
                        }

                        // if refunded amount is greather then split payment amount do a full refund
                        if ($amount >= $splitPaymentAmount) {
                            $modificationAmount = $splitPaymentAmount;
                        } else {
                            $modificationAmount = $amount;
                        }
                        // update amount with rest of the available amount
                        $amount = $amount - $splitPaymentAmount;
                    }

                    $modificationAmountObject = [
                        'currency' => $currency,
                        'value' => $this->ebanxHelper->formatAmount($modificationAmount, $currency)
                    ];

                    $result[] = [
                        "modificationAmount" => $modificationAmountObject,
                        "reference" => $payment->getOrder()->getIncrementId(),
                        "originalReference" => $splitPayment->getToken()
                    ];
                }
            }
        } else {
            //format the amount to minor units
            $amount = $this->ebanxHelper->formatAmount($amount, $currency);
            $modificationAmount = ['currency' => $currency, 'value' => $amount];

            $result = [
                [
                    "modificationAmount" => $modificationAmount,
                    "reference" => $payment->getOrder()->getIncrementId(),
                    "originalReference" => $token
                ]
            ];
        }

        return $result;
    }
}