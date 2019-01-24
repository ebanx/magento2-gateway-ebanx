<?php
namespace DigitalHub\Ebanx\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

class Data extends AbstractHelper
{
    private $_moduleList;

    public function __construct(Context $context, ModuleListInterface $moduleList)
    {
        parent::__construct($context);
        $this->_moduleList = $moduleList;
    }

    public function getConfigData($area, $field, $storeId = null)
    {
        return $this->scopeConfig->getValue('payment/' . $area . '/' . $field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPersonType($document, $country)
    {
        $document = str_replace(array('.', '-', '/'), '', $document);

        if ($country !== 'BR' || strlen($document) < 14) {
            return \Ebanx\Benjamin\Models\Person::TYPE_PERSONAL;
        }
        return \Ebanx\Benjamin\Models\Person::TYPE_BUSINESS;
    }

    public function getCustomerDocumentNumber($quote, $customField)
    {
        $customer = $quote->getCustomer();

        // Search for custom attribute
        if($customField && $customer->getCustomAttribute($customField)){
            return $customer->getCustomAttribute($customField)->getValue();
        }

        // Search for existing attribute getter on Customer Data API
        $value = false;
        try {
            $value = call_user_func(array($customer, 'get' . $this->_parseToCamelCase($customField)));
        } catch (\Exception $e){ }

        if($value){
            return $value;
        }

        // search for billing address (guest checkout or not logged customer)

        // taxvat
        if($customField == 'taxvat'){
            if($quote->getBillingAddress()->getData('taxvat')){
                return $quote->getBillingAddress()->getData('taxvat');
            } else if($quote->getBillingAddress()->getData('vat_id')){
                return $quote->getBillingAddress()->getData('vat_id');
            }
        }
        return $quote->getBillingAddress()->getData($customField);
    }

    public function getCustomerDocumentNumberField($quote)
    {
        $customField = false;
        $countryId = $quote->getBillingAddress()->getCountryId();

        if($countryId == 'BR'){
            // cnpj
            $cnpjField = $this->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_brazil_cnpj');
            $cpfField = $this->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_brazil_cpf');

            // cnpj value exists?
            if($this->getCustomerDocumentNumber($quote, $cnpjField)){
                // send cnpj field
                $customField = $cnpjField;
            } else {
                // send cpf field
                $customField = $cpfField;
            }
        }

        if($countryId == 'CL'){
            $customField = $this->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_chile');
        }

        if($countryId == 'AR'){
            $customField = $this->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_argentina');
        }

        if($countryId == 'CO'){
            $customField = $this->getConfigData('digitalhub_ebanx_global/customer_fields', 'document_field_colombia');
        }

        return $customField;
    }

    public function _parseToCamelCase($string)
    {
        $words = explode('_', $string);
        $words = array_map("ucwords", $words);
        return implode('', $words);
    }

    public function getAddressData($field, $addressObject)
    {
        $customField = $this->getConfigData('digitalhub_ebanx_global/customer_fields', $field);
        $addressData = $addressObject->getData();

        if(in_array($customField, ['street_1','street_2','street_3','street_4'])){
            $line = (int)str_replace('street_', '', $customField);
            return $addressObject->getStreetLine($line);
        } else {
            if(isset($addressData[$customField])){
                return $addressData[$customField];
            }
        }
        return null;
    }

    public function getFullAddressData($addressObject) {
        return $addressObject->getData()['street'];
    }

    public function getInterestRateFor($number)
    {
        $interest_rates_enabled = json_decode($this->getConfigData('digitalhub_ebanx_global/cc', 'enable_interest_rate'));
        $interest_rates_config = json_decode($this->getConfigData('digitalhub_ebanx_global/cc', 'interest_rates'));

        if((int)$interest_rates_enabled){
            foreach($interest_rates_config as $item){
                if($item->number == $number){
                    return $item->value;
                }
            }
        }

        return 0;
    }

    public function calculateTotalWithInterest($total, $installments_number)
    {
        $interest_rate = (float)$this->getInterestRateFor($installments_number);
        if($interest_rate){
            $total = (floatval($interest_rate / 100) * floatval($total) + floatval($total));
        }
        return $total;
    }

    public function getMinInstallmentValue($country)
    {
        $configValue = (float)$this->getConfigData('digitalhub_ebanx_global/cc', 'min_installment_value');

        if($country == 'BR' && $configValue < 5){
            return 5;
        }

        if($country == 'MX' && $configValue < 100){
            return 100;
        }

        return $configValue;
    }

    public function getModuleVersion() {
        return $this->_moduleList->getOne($this->_getModuleName())['setup_version'];
    }
}
