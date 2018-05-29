define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        'use strict';

        return function (method) {
            return storage.get(url.build('digitalhub_ebanx/oneclickpayment/address'), false)
        };
    }
);
