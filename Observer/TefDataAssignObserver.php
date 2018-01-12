<?php

namespace Ebanx\Payments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class TefDataAssignObserver extends BaseDataAssignObserver
{
    const SELECTED_BANK = 'selected_bank';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::SELECTED_BANK,
    ];
}
