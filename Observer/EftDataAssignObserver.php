<?php

namespace Ebanx\Payments\Observer;

class EftDataAssignObserver extends BaseDataAssignObserver
{
    const SELECTED_BANK = 'selected_bank';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::SELECTED_BANK,
    ];
}
