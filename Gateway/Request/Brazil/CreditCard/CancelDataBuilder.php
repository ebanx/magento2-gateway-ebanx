<?php
namespace DigitalHub\Ebanx\Gateway\Request\Brazil\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
// use DigitalHub\Ebanx\Observer\Brazil\CreditCard\DataAssignObserver;

/**
 * Class CancelDataBuilder
 */
class CancelDataBuilder implements BuilderInterface
{

    private $_ebanxHelper;
    private $_logger;

    /**
     * CancelDataBuilder constructor.
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
        $this->_logger->info('CancelDataBuilder :: __construct');
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

        $payment = $paymentDataObject->getPayment();
        $transaction_data = $payment->getAdditionalInformation('transaction_data');

        $request = [
            'payment_hash' => $transaction_data['payment']['hash']
        ];

        $this->_logger->info('CancelDataBuilder :: build', $request);

        return $request;
    }
}
