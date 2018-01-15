<?php

namespace Ebanx\Payments\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class InterestRates extends AbstractFieldArray
{

    /**
     * @var Installment
     */
    protected $_installmentRenderer = null;

    /**
     * Return renderer for installments
     *
     * @return Installment|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getNumberOfInstallmentsRenderer()
    {
        if (!$this->_installmentRenderer) {
            $this->_installmentRenderer = $this->getLayout()->createBlock(
                '\Adyen\Payment\Block\Adminhtml\System\Config\Field\Installment',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_installmentRenderer;
    }

    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'installments',
            [
                'label'     => __('Up To'),
                'renderer'  => $this->getNumberOfInstallmentsRenderer(),
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
        $installlments = $row->getInstallments();

        $options = [];
        if ($installlments) {
            $options['option_' . $this->getNumberOfInstallmentsRenderer()->calcOptionHash($installlments)]
                = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
        return;
    }
}
