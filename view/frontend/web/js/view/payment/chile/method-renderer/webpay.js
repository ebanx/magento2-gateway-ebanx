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
                        'totalLocalCurrency',
                        'showDocumentFields'
                    ]);
                return this;
            },

            initialize: function() {
                var self = this;
                this._super();

                // document number verification promise
                $.when(documentNumberVerification()).done(function (result) {
                    self.showDocumentFields(!result.has_document_number)
                });

                $.when(totalLocalCurrency()).done(function (result) {
                    if(self.getGlobalConfig().show_iof && result.total_with_iof_formatted){
                        var text = $t('Total amount in local currency with IOF (0.38%):');
                        self.totalLocalCurrency(text + ' ' + result.total_with_iof_formatted);
                    } else {
                        var text = $t('Total amount in local currency:');
                        self.totalLocalCurrency(text + ' ' + result.total_formatted);
                    }
                });
            },

            getFormTemplate: function(){
                return 'DigitalHub_Ebanx/payment/chile/webpay/form'
            },

            getData: function() {
                return {
                    method: this.getCode(),
                    additional_data: {
                        'document_number': this.documentNumber()
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

            getGlobalConfig: function() {
                return window.checkoutConfig.payment.digitalhub_ebanx_global
            },

            getMethodConfig: function() {
                return window.checkoutConfig.payment.digitalhub_ebanx_chile_webpay
            },

            showDocumentTypeField: function(){
                return false;
            },

            afterPlaceOrder: function() {
                redirectOnSuccessAction.redirectUrl = url.build('digitalhub_ebanx/payment/redirect');
                this.redirectAfterPlaceOrder = true;
            }
        });
    }
);
