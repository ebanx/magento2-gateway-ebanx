<?php
namespace DigitalHub\Ebanx\Model\Adminhtml\Source\Address;

class Fields implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $model = $objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection');
        $model->setEntityTypeFilter(2);

        $options = [
            [
                'label' => '',
                'value' => ''
            ],
            [
                'label' => 'Street 1',
                'value' => 'street_1'
            ],
            [
                'label' => 'Street 2',
                'value' => 'street_2'
            ],
            [
                'label' => 'Street 3',
                'value' => 'street_3'
            ],
            [
                'label' => 'Street 4',
                'value' => 'street_4'
            ]
        ];

        foreach($model->getData() as $item){
            if($item['attribute_code'] != 'street'){
                $options[] = [
                    'label' => $item['frontend_label'],
                    'value' => $item['attribute_code']
                ];
            }
        }

        return $options;
    }
}
