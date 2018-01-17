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
                template: 'Ebanx_Payments/payment/ebanx_boleto',
                paymentDocument: window.checkoutConfig.payment.ebanx.customerDocument
            },
            initialize: function () {
              this._super();
              documentMask('#ebanx_boleto_document');
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
                this.disableBtnPlaceOrder();
                if (!this.validateForm('#' + this.getCode() + '_document_form')) {
                    this.enableBtnPlaceOrder();
                    return;
                }

                this.setDocument(data.paymentDocument);
                this.placeOrder();
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
            disableBtnPlaceOrder: function(){
                $('#btn_boleto_form_place_order').attr('disabled', 'disabled');
            },
            enableBtnPlaceOrder: function(){
                $('#btn_boleto_form_place_order').removeAttr('disabled');
            }
        });
    }
);
