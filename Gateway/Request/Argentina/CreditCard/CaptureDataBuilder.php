<?php
namespace DigitalHub\Ebanx\Gateway\Request\Argentina\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
// use DigitalHub\Ebanx\Observer\Argentina\CreditCard\DataAssignObserver;

/**
 * Class CaptureDataBuilder
 */
class CaptureDataBuilder implements BuilderInterface
{

    private $_ebanxHelper;
    private $_logger;

    /**
     * CaptureDataBuilder constructor.
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
        $this->_logger->info('CaptureDataBuilder :: __construct');
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

        $this->_logger->info('CaptureDataBuilder :: build', $request);

        return $request;
    }
}
