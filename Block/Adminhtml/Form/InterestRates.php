<?php
namespace DigitalHub\Ebanx\Block\Adminhtml\Form;

class InterestRates extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var $_numberOptions \DigitalHub\Ebanx\Block\Adminhtml\Form\Field\Activation
     */
    protected $_numberOptions;

    /**
     * Get activation options.
     *
     * @return \DigitalHub\Ebanx\Block\Adminhtml\Form\Field\Activation
     */
    protected function _getNumberRenderer()
    {
        if (!$this->_numberOptions) {
            $this->_numberOptions = $this->getLayout()->createBlock(
                '\DigitalHub\Ebanx\Block\Adminhtml\Form\InterestRates\Number',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->_numberOptions;
   }

    /**
     * Prepare to render.
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'number',
            [
                'label' => __('Number'),
                'renderer' => $this->_getNumberRenderer()
            ]
        );
        $this->addColumn('value', ['label' => __('Value (monthly %)')]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object.
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $options = [];
        $customOptions = $row->getData('number');

        $key = 'option_' . $this->_getNumberRenderer()->calcOptionHash($customOptions);
        $options[$key] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }
}
