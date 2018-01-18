/*browser:true*/
/*global define*/

define(
  [
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'eft',
  ],
  function(Component, $, eft) {
    'use strict';
    return Component.extend({
      defaults: {
        template: 'Ebanx_Payments/payment/ebanx_eft',
        eftSelectedBank: 'banco_agrario',
        availableBanks: window.checkoutConfig.payment.ebanx.availableBanks,
      },
      getData: function() {
        return {
          'method': this.getCode(),
          'additional_data': {
            'eft_selected_bank': this.eftSelectedBank,
          },
        };
      },
      initialize: function() {
        this._super();
        eft.populateBankSelectWithBanks('#bank-select-ebanx-eft', this.availableBanks);
      },
      setEftSelectedBank: function(eftSelectedBank) {
        this.eftSelectedBank = eftSelectedBank;
      },
      beforePlaceOrder: function(data) {
        this.disableBtnPlaceOrder();
        if (!this.validateForm('#' + this.getCode() + '_form')) {
          this.enableBtnPlaceOrder();
          return;
        }
        this.setEftSelectedBank(data.eftSelectedBank);
        this.placeOrder();
      },
      validateForm: function(form) {
        return $(form).validation() && $(form).validation('isValid');
      },
      disableBtnPlaceOrder: function() {
        $('#btn_eft_form_place_order').attr('disabled', 'disabled');
      },
      enableBtnPlaceOrder: function() {
        $('#btn_eft_form_place_order').removeAttr('disabled');
      },
    });
  }
);
