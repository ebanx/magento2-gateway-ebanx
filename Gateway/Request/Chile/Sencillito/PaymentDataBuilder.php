<?php
namespace DigitalHub\Ebanx\Gateway\Request\Chile\Sencillito;

use Magento\Payment\Gateway\Request\BuilderInterface;
use DigitalHub\Ebanx\Observer\Chile\Sencillito\DataAssignObserver;

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
        $payment = $paymentDataObject->getPayment();
        $order = $paymentDataObject->getOrder();
        $storeId = $order->getStoreId();

        // $this->_logger->info('Request::build order', [$order->getOrderIncrementId()]);
        // $this->_logger->info('Request::build payment', $payment->getData());

        $additionalData = $payment->getAdditionalInformation();

        $this->_logger->info('PaymentDataBuilder :: build');

        $days = (int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cash', 'cash_expiration_days');
        $dueDate = new \DateTime(date('Y-m-d', strtotime('now +' . $days . 'days')) . ' 00:00:00');

        $request = [
            'type' => 'sencillito',
            'dueDate' => $dueDate,
            'amountTotal' => $order->getGrandTotalAmount(),
        ];

        $this->_logger->info('PaymentDataBuilder :: build', $request);

        return $request;
    }
}
