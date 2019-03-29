<?php
namespace DigitalHub\Ebanx\Gateway\Request\Brazil\EbanxAccount;

use Magento\Payment\Gateway\Request\BuilderInterface;

class PaymentDataBuilder implements BuilderInterface
{
    private $_ebanxHelper;
    private $_logger;
    private $_session;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * PaymentDataBuilder constructor.
     *
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \DigitalHub\Ebanx\Logger\Logger $logger
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Framework\Model\Context $context,
        \DigitalHub\Ebanx\Logger\Logger $logger,
        \Magento\Checkout\Model\Session $session
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;
        $this->_session = $session;
        $this->appState = $context->getAppState();

        $this->_logger->info('PaymentDataBuilder :: __construct');
    }

    /**
     * @param array $buildSubject
     * @return mixed
     */
    public function build(array $buildSubject)
    {
        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject $paymentDataObject */
        $paymentDataObject = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($buildSubject);
        $order = $paymentDataObject->getOrder();

        $this->_logger->info('PaymentDataBuilder :: build');

        $request = [
            'type' => 'ebanxaccount',
            'amountTotal' => $order->getGrandTotalAmount(),
        ];

        $this->_logger->info('PaymentDataBuilder :: build', $request);

        return $request;
    }
}
