define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        'use strict';

        return function () {
            return storage.get(url.build('digitalhub_ebanx/oneclickpayment/sessioncheck'), false)
        };
    }
);
