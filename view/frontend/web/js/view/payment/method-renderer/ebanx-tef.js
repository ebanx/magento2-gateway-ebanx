/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component, urlBuilder) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ebanx_Payments/payment/ebanx_tef'
            },
            afterPlaceOrder: function () {
                window.location.href = urlBuilder.build('ebanx/tef/redirect');
                this.placeOrder();

                return false;
            }
        });
    }
);
