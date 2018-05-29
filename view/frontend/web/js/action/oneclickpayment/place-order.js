define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        'use strict';

        return function (data) {
            return storage.post(url.build('digitalhub_ebanx/oneclickpayment/placeorder'), JSON.stringify(data), true)
        };
    }
);
