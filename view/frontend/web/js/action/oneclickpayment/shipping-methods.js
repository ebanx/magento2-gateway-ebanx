define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        'use strict';

        return function (cart_id, address_id) {
            return storage.get(url.build('digitalhub_ebanx/oneclickpayment/shippingmethods?cart_id=' + cart_id + '&address_id=' + address_id), false)
        };
    }
);
