<?php

namespace Ebanx\Payments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class DocumentDataAssignObserver extends BaseDataAssignObserver
{
    const DOCUMENT = 'document';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::DOCUMENT,
    ];
}
