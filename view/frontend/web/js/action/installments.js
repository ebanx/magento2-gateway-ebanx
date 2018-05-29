define(
    [
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/quote'
    ],
    function (storage, url, quote) {
        'use strict';

        return function () {
            return storage.post(url.build('digitalhub_ebanx/creditcard/installments/total/' + quote.totals().grand_total), false)
        };
    }
);
