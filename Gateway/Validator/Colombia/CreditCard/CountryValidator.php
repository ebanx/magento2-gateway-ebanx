<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Colombia\CreditCard;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class CountryValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator
{
    /**
     * @var \DigitalHub\Ebanx\Helper\Data
     */
    private $_ebanxHelper;

    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param \DigitalHub\Ebanx\Helper\Data $ebanxHelper
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper
    ) {
        $this->_ebanxHelper = $ebanxHelper;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return bool
     * @throws NotFoundException
     * @throws \Exception
     */
    public function validate(array $validationSubject)
    {
        $isValid = false;
        $storeId = $validationSubject['storeId'];

        $country = $validationSubject['country'];

        if($country == 'CO'){
            $isValid = true;
        }

        return $this->createResult($isValid);
    }
}
