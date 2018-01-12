/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
    ],
    function (Component, $) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ebanx_Payments/payment/ebanx_safetypay',
                safetypayType: 'online',
            },
            getData: function() {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'safetypay_type': this.safetypayType,
                    },
                };
            },
            setSafetypayType: function (safetypayType) {
                this.safetypayType = safetypayType;
            },
            beforePlaceOrder: function (data) {
                this.disableBtnPlaceOrder();
                if (!this.validateForm('#' + this.getCode() + '_form')) {
                    this.enableBtnPlaceOrder();
                    return;
                }

                this.setSafetypayType(data.safetypayType);
                this.placeOrder();
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
            disableBtnPlaceOrder: function(){
                $('#btn_safetypay_form_place_order').attr('disabled', 'disabled');
            },
            enableBtnPlaceOrder: function(){
                $('#btn_safetypay_form_place_order').removeAttr('disabled');
            }
        });
    }
);
