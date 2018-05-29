<?php
namespace DigitalHub\Ebanx\Gateway\Request\Mexico\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
// use DigitalHub\Ebanx\Observer\Mexico\CreditCard\DataAssignObserver;

/**
 * Class RefundDataBuilder
 */
class RefundDataBuilder implements BuilderInterface
{

    private $_ebanxHelper;
    private $_logger;

    /**
     * RefundDataBuilder constructor.
     *
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param \DigitalHub\Ebanx\Helper\Data $logger
     */
    public function __construct(
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;
        $this->_logger->info('RefundDataBuilder :: __construct');
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
        $amount =  \Magento\Payment\Gateway\Helper\SubjectReader::readAmount($buildSubject);

        $payment = $paymentDataObject->getPayment();
        $transaction_data = $payment->getAdditionalInformation('transaction_data');

        $request = [
            'amount' => $amount,
            'payment_hash' => $transaction_data['payment']['hash']
        ];

        $this->_logger->info('RefundDataBuilder :: build', $request);

        return $request;
    }
}
