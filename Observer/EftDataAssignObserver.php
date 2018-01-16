<?php

namespace Ebanx\Payments\Observer;

class EftDataAssignObserver extends BaseDataAssignObserver
{
    const EFT_SELECTED_BANK = 'eft_selected_bank';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::EFT_SELECTED_BANK,
    ];
}
