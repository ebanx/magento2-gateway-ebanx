<?php
namespace DigitalHub\Ebanx\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class OrderDataBuilder
 */
class OrderDataBuilder implements BuilderInterface
{

    private $_ebanxHelper;
    private $_logger;

    /**
     * OrderDataBuilder constructor.
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
        $this->_logger->info('OrderDataBuilder :: __construct');
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

        $order = $paymentDataObject->getOrder();

        $orderItems = [];
        foreach($order->getItems() as $item){
            $orderItems[] = new \Ebanx\Benjamin\Models\Item([
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'unitPrice' => $item->getPrice(),
                'quantity' => $item->getQtyOrdered()
            ]);
        }

        $request = [
            'orderNumber' => $order->getOrderIncrementId(),
            'merchantPaymentCode' => $order->getOrderIncrementId() . '_' . time(),
            'items' => $orderItems,
            'userValues' => array(
               1 => 'from_magento2',
               3 => $this->_ebanxHelper->getModuleVersion(),
           )
        ];

        $this->_logger->info('OrderDataBuilder :: build', $request);

        return $request;
    }
}
