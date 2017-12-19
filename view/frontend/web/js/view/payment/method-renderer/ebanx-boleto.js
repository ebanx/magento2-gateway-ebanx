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
                paymentDocument: null
            },
            getData: function () {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'document': this.paymentDocument
                    }
                }
            },
            setDocument: function (paymentDocument) {
                this.paymentDocument = paymentDocument;
            },
            beforePlaceOrder: function (data) {
                if (!this.validateForm('#document-form')) {
                    return;
                }

                this.setDocument(data.paymentDocument);
                this.placeOrder();
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            }
        });
    }
);
