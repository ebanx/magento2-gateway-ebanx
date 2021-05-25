<?php
namespace DigitalHub\Ebanx\Gateway\Request\Brazil\Pix;

use Magento\Payment\Gateway\Request\BuilderInterface;

class PaymentDataBuilder implements BuilderInterface
{
    private $_ebanxHelper;
    private $_logger;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * PaymentDataBuilder constructor.
     *
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     * @param \Magento\Framework\Model\Context $context
     */
    public function __construct(
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Framework\Model\Context $context,
        \DigitalHub\Ebanx\Logger\Logger $logger
    )
    {
        $this->_ebanxHelper = $ebanxHelper;
        $this->_logger = $logger;
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

        $days = (int)$this->_ebanxHelper->getConfigData('digitalhub_ebanx_global/cash', 'cash_expiration_days');
        $dueDate = new \DateTime(date('Y-m-d', strtotime('now +' . $days . 'days')) . ' 00:00:00');

        $request = [
            'type' => 'pix',
            'dueDate' => $dueDate,
            'amountTotal' => $order->getGrandTotalAmount(),
        ];

        $this->_logger->info('PaymentDataBuilder :: build', $request);

        return $request;
    }
}
