/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery'
    ],
    function (Component, $) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ebanx_Payments/payment/ebanx_creditcard_br',
                brand: null,
                cvv: null,
                instalments: 1
            },
            getData: function() {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'brand': this.brand,
                        'cvv': this.cvv,
                        'instalments': this.instalments
                    }
                };
            },
            setCardData: function (data) {
                this.brand = data.brand;
                this.cvv = data.cvv;
                this.instalments = data.instalments;
            },
            beforePlaceOrder: function (data) {
                if (!this.validateForm('#card-form')) {
                    return;
                }

                this.setCardData(data);
                this.placeOrder();
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
        });
    }
);
