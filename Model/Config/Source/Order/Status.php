<?php
namespace Ebanx\Payments\Model\Config\Source\Order;

use Magento\Framework\Option\ArrayInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

class Status implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private $statusCollectionFactory;

    /**
     * Status constructor.
     *
     * @param CollectionFactory $statusCollectionFactory
     */
    public function __construct(CollectionFactory $statusCollectionFactory)
    {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    public function toOptionArray()
    {
        return array_map(function($tag) {
            return array(
                'label' => $tag['label'],
                'value' => $tag['status']
            );
        }, $this->statusCollectionFactory->create()->getData());
    }
}
