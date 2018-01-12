<?php

namespace Ebanx\Payments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;


class CreditCardDataAssignObserver extends BaseDataAssignObserver
{
    const BRAND = 'brand';
    const TOKEN = 'token';
    const INSTALMENTS = 'instalments';
    const CVV = 'cvv';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::BRAND,
        self::TOKEN,
        self::INSTALMENTS,
        self::CVV,
    ];
}
