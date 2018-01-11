/*browser:true*/
/*global define*/
/*global checkoutConfig*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'eft',
    ],
    function (Component, $, eft) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Ebanx_Payments/payment/ebanx_eft',
                selectedBank: 'banco_agrario',
                availableBanks: window.checkoutConfig.payment.ebanx.availableBanks
            },
            getData: function() {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'selected_bank': this.selectedBank,
                    }
                };
            },
            initialize: function () {
                this._super();
                eft.populateBankSelectWithBanks('#bank-select-ebanx-eft', this.availableBanks);
            },
            setSelectedBank: function (selectedBank) {
                this.selectedBank = selectedBank;
            },
            beforePlaceOrder: function (data) {
                this.disableBtnPlaceOrder();
                if (!this.validateForm('#' + this.getCode() + '_form')) {
                    this.enableBtnPlaceOrder();
                    return;
                }
                console.log(data);
                this.setSelectedBank(data.selectedBank);
                this.placeOrder();
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
            disableBtnPlaceOrder: function(){
                $('#btn_eft_form_place_order').attr('disabled', 'disabled');
            },
            enableBtnPlaceOrder: function(){
                $('#btn_eft_form_place_order').removeAttr('disabled');
            }
        });
    }
);
