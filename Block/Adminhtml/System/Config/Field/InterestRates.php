<?php

namespace Ebanx\Payments\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class InterestRates extends AbstractFieldArray
{

    /**
     * @var Instalment
     */
    protected $_instalmentRenderer = null;

    /**
     * Return renderer for installments
     *
     * @return Instalment|\Magento\Framework\View\Element\BlockInterface
     */
    protected function getNumberOfInstalmentsRenderer()
    {
        if ($this->_instalmentRenderer) {
            return $this->_instalmentRenderer;
        }

        try {
            $this->_instalmentRenderer = $this->getLayout()->createBlock(
                '\Ebanx\Payments\Block\Adminhtml\System\Config\Field\Instalment',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        } catch (LocalizedException $e) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
            // TODO: Log this exception and unignore this statement
        }

        return $this->_instalmentRenderer;
    }

    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'instalments',
            [
                'label'     => __('Up To'),
                'renderer'  => $this->getNumberOfInstalmentsRenderer(),
            ]
        );
        $this->addColumn(
            'interest_rate',
            [
                'label' => __('Interest'),
                'renderer'  => false,
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow( DataObject $row)
    {
        $instalments = $row->getInstalments();

        $options = [];
        if ($instalments) {
            $options['option_' . $this->getNumberOfInstalmentsRenderer()->calcOptionHash($instalments)]
                = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
        return;
    }
}
