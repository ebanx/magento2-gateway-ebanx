<?php
namespace DigitalHub\Ebanx\Gateway\Request\Brazil\Pix;

use Magento\Payment\Gateway\Request\BuilderInterface;

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
     * @param \DigitalHub\Ebanx\Logger\Logger $logger
     */
    public function __construct(
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
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
