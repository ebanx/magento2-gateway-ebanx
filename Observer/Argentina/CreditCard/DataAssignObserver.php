<?php
namespace DigitalHub\Ebanx\Observer\Argentina\CreditCard;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class DataAssignObserver
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    const TOKEN = 'token';
    const CVV = 'cvv';
    const MASKED_CARD_NUMBER = 'masked_card_number';
    const PAYMENT_TYPE_CODE = 'payment_type_code';
    const INSTALLMENTS = 'installments';
    const DOCUMENT_TYPE = 'document_type';
    const DOCUMENT_NUMBER = 'document_number';
    const SAVE_CC = 'save_cc';
    const USE_SAVED_CC = 'use_saved_cc';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::TOKEN,
        self::CVV,
        self::MASKED_CARD_NUMBER,
        self::PAYMENT_TYPE_CODE,
        self::INSTALLMENTS,
        self::DOCUMENT_TYPE,
        self::DOCUMENT_NUMBER,
        self::SAVE_CC,
        self::USE_SAVED_CC,
    ];
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
