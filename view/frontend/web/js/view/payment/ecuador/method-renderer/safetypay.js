define(
    [
        'underscore',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/model/payment/additional-validators',
        'DigitalHub_Ebanx/js/action/document-number-verification',
        'mage/translate',
        'mage/url',
        'jquery',
        'DigitalHub_Ebanx/js/action/total-local-currency'
    ],
    function (
        _,
        Component,
        checkoutData,
        quote,
        priceUtils,
        fullScreenLoader,
        redirectOnSuccessAction,
        additionalValidators,
        documentNumberVerification,
        $t,
        url,
        $,
        totalLocalCurrency
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'DigitalHub_Ebanx/payment/base-form-redirect',
                documentNumber: '',
                totalLocalCurrency: '',
                safetypayType: '',
                showDocumentFields: false
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'documentNumber',
                        'safetypayType',
                        'totalLocalCurrency',
                        'showDocumentFields'
                    ]);
                return this;
            },

            initialize: function() {
                var self = this;
                this._super();

                // this method not uses document number
                this.showDocumentFields(false)

                $(document).on('DOMSubtreeModified', "tr.grand.totals > td > strong > span", function () {
                    self.setLocalTotal(self);
                });
                self.setLocalTotal(self);
            },

            getFormTemplate: function(){
                return 'DigitalHub_Ebanx/payment/ecuador/safetypay/form'
            },

            getData: function() {
                return {
                    method: this.getCode(),
                    additional_data: {
                        'document_number': this.documentNumber(),
                        'safetypay_type': this.safetypayType()
                    }
                };
            },

            isActive: function () {
                return true;
            },

            getInfoTotalFormated: function(use_iof){
                var total = window.checkoutConfig.totalsData.grand_total
                if(use_iof){
                    total += total * 0.0038
                }
                return priceUtils.formatPrice(total, quote.getPriceFormat())
            },

            getSafetypayTypeList: function(){
                return [
                    {label: $t('Cash'), value: 'cash'},
                    {label: $t('On-line'), value: 'online'}
                ]
            },

            getGlobalConfig: function() {
                return window.checkoutConfig.payment.digitalhub_ebanx_global
            },

            getMethodConfig: function() {
                return window.checkoutConfig.payment.digitalhub_ebanx_ecuador_safetypay
            },

            showDocumentTypeField: function(){
                return false;
            },

            afterPlaceOrder: function() {
                redirectOnSuccessAction.redirectUrl = url.build('digitalhub_ebanx/payment/redirect');
                this.redirectAfterPlaceOrder = true;
            },

            beforePlaceOrder: function() {
                this.placeOrder();
            },

            getMask: function() {
                return true;
            },

            setLocalTotal: function (self) {
                $.when(totalLocalCurrency()).done(function (result) {
                    var text = $t('Total amount in local currency:');
                    self.totalLocalCurrency(text + ' ' + result.total_formatted);
                });
            },
        });
    }
);
