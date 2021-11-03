<?php
namespace DigitalHub\Ebanx\Gateway\Validator\Mexico\Oxxo;

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
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        \DigitalHub\Ebanx\Helper\Data $ebanxHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_ebanxHelper = $ebanxHelper;
        $this->storeManager = $storeManager;
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

        $country = $validationSubject['country'];

        $available_currencies = ['USD','MXN'];
        if($country == 'MX' && in_array($this->storeManager->getStore()->getBaseCurrencyCode(), $available_currencies)){
            $isValid = true;
        }

        if (!$isValid) {
            $this->_logger->info('Country is not MX or the store base currency is not in the available currencies');
        }

        return $this->createResult($isValid);
    }
}
