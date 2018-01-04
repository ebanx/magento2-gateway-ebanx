/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'document-mask'
    ],
    function (Component, $, documentMask) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ebanx_Payments/payment/ebanx_wallet',
                paymentDocument: window.checkoutConfig.payment.ebanx.customerDocument
            },
            initialize: function () {
                this._super();
                documentMask('#ebanx_wallet_document');
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
                if (!this.validateForm('#' + this.getCode() + '_document_form')) {
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
