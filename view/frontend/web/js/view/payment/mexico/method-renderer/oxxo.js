define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'DigitalHub_Ebanx/js/action/document-number-verification',
        'jquery',
        'mage/translate',
        'DigitalHub_Ebanx/js/action/total-local-currency'
    ],
    function (Component, quote, priceUtils, documentNumberVerification, $, $t, totalLocalCurrency) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'DigitalHub_Ebanx/payment/base-form',
                totalLocalCurrency: '',
                showDocumentFields: false
            },

            initialize: function(){
                var self = this;
                this._super();

                // this method not uses document number
                this.showDocumentFields(false)

                // // document number verification promise
                // $.when(documentNumberVerification()).done(function (result) {
                //     self.showDocumentFields(!result.has_document_number)
                // });

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

            initObservable: function () {
                this._super()
                    .observe([
                        'documentNumber',
                        'totalLocalCurrency',
                        'showDocumentFields'
                    ]);
                return this;
            },

            getData: function() {
                return {
                    method: this.getCode(),
                    additional_data: {
                        'document_number': this.documentNumber()
                    }
                };
            },

            isActive: function(){
                return true;
            },

            getFormTemplate: function(){
                return 'DigitalHub_Ebanx/payment/mexico/oxxo/form'
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
                return window.checkoutConfig.payment.digitalhub_ebanx_mexico_oxxo
            },

            showDocumentTypeField: function(){
                return false;
            },

            beforePlaceOrder: function(){
                this.placeOrder();
            }
        });
    }
);
