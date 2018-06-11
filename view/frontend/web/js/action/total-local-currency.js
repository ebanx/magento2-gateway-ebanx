define(
    [
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/quote'
    ],
    function (storage, url, quote) {
        'use strict';

        return function () {
            var installments = arguments[0] && parseInt(arguments[0]) || 1;
            return storage.get(url.build('digitalhub_ebanx/checkout/exchange?installments=' + installments), false)
        };
    }
);
