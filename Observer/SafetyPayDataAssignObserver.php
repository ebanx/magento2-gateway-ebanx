<?php

namespace Ebanx\Payments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\PaymentInterface;

class SafetyPayDataAssignObserver extends BaseDataAssignObserver
{
    const SAFETYPAY_TYPE = 'safetypay_type';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::SAFETYPAY_TYPE,
    ];
}
