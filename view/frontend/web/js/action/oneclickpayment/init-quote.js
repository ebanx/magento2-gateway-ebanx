define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        'use strict';

        return function (quoteData) {
            return storage.post(url.build('digitalhub_ebanx/oneclickpayment/initquote'), JSON.stringify(quoteData), true)
        };
    }
);
