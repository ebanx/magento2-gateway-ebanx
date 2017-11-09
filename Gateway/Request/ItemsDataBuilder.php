<?php
namespace Ebanx\Payments\Gateway\Request;

use Ebanx\Benjamin\Models\Address;
use Ebanx\Benjamin\Models\Item;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Ebanx\Payments\Helper\Data as EbanxData;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class ItemsDataBuilder
 */
class ItemsDataBuilder implements BuilderInterface
{
    /**
     * @var \Ebanx\Payments\Helper\Data
     */
    private $ebanxHelper;

    /**
     * ItemsDataBuilder constructor.
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

        $items = $paymentDataObject->getOrder()->getItems();

        $itemsArray = [];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($items as $item) {
            $itemsArray[] = new Item([
                'sku' => $item->getSku(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'unitPrice' => $item->getPrice(),
                'quantity' => $item->getQtyOrdered(),
            ]);
        }

        return [
            'items' => $itemsArray,
        ];
    }
}
