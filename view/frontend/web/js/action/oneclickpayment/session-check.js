define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        'use strict';
        return function () {
            url.setBaseUrl(window.oneClickPaymentMagentoBaseUrl);
            return storage.get(url.build('digitalhub_ebanx/oneclickpayment/sessioncheck'), false)
        };
    }
);
