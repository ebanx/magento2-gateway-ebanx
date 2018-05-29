<?php
namespace DigitalHub\Ebanx\Block\Adminhtml\Form\InterestRates;

class Number extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @param string $value
     * @return DigitalHub\Ebanx\Block\Adminhtml\Form\InterestRates\Number
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Parse to html
     *
     * @return mixed
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach (range(1,12) as $number) {
                $this->addOption($number, $number . 'x');
            }
        }

        return parent::_toHtml();
    }
}
