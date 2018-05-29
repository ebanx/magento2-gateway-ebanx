define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        'use strict';

        return function (method) {
            return storage.post(url.build('digitalhub_ebanx/creditcard/checkout_saved/method/' + method), false)
        };
    }
);
