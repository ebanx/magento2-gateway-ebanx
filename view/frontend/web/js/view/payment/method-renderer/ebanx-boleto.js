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
                template: 'Ebanx_Payments/payment/ebanx_boleto',
                payment_document: null
            },
            getData: function () {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'document': this.payment_document
                    }
                }
            },
            setCardData: function (data) {
                this.payment_document = data.payment_document;
            },
            beforePlaceOrder: function (data) {
                if (!this.validateForm('#document-form')) {
                    return;
                }

                this.setCardData(data);
                this.placeOrder();
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            }
        });
    }
);
