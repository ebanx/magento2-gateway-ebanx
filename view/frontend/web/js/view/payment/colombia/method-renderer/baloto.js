define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'DigitalHub_Ebanx/js/action/document-number-verification',
        'jquery',
        'mage/translate',
        'DigitalHub_Ebanx/js/action/total-local-currency',
        'DigitalHub_Ebanx/js/view/payment/colombia/document-mask',
        'DigitalHub_Ebanx/js/view/payment/colombia/document-validator',
    ],
    function (Component, quote, priceUtils, documentNumberVerification, $, $t, totalLocalCurrency, documentMask, validDocument) {
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

                // document number verification promise
                $.when(documentNumberVerification()).done(function (result) {
                    self.showDocumentFields(!result.has_document_number)
                });

                $(document).on('DOMSubtreeModified', "tr.grand.totals > td > strong > span", function () {
                    self.setLocalTotal(self);
                });
                self.setLocalTotal(self);
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
                return 'DigitalHub_Ebanx/payment/colombia/baloto/form'
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
                return window.checkoutConfig.payment.digitalhub_ebanx_colombia_baloto
            },

            showDocumentTypeField: function(){
                return false;
            },

            beforePlaceOrder: function(){
                if(validDocument(document.querySelector('#digitalhub_ebanx_colombia_baloto_document_number').value)){
                    this.placeOrder();
                } else {
                    this.messageContainer.addErrorMessage({message: $t('Invalid Document Length')});
                }
            },

            getMask: function() {
                documentMask();
            },

            setLocalTotal: function (self) {
                $.when(totalLocalCurrency()).done(function (result) {
                    var text = $t('Total amount in local currency:');
                    self.totalLocalCurrency(text + ' ' + result.total_formatted);
                });
            }
        });
    }
);
