define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'DigitalHub_Ebanx/js/action/document-number-verification',
        'jquery',
        'mage/translate',
        'DigitalHub_Ebanx/js/action/total-local-currency',
        'DigitalHub_Ebanx/js/view/payment/brazil/document-mask',
        'DigitalHub_Ebanx/js/view/payment/brazil/document-validator',
    ],
    function (Component, quote, priceUtils, documentNumberVerification, $, $t, totalLocalCurrency, documentMask, validDocument) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'DigitalHub_Ebanx/payment/base-form',
                showDocumentFields: false,
                totalLocalCurrency: ''
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
                        'showDocumentFields',
                        'totalLocalCurrency'
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
                return 'DigitalHub_Ebanx/payment/brazil/boleto/form'
            },

            getGlobalConfig: function() {
                return window.checkoutConfig.payment.digitalhub_ebanx_global
            },

            getMethodConfig: function() {
                return window.checkoutConfig.payment.digitalhub_ebanx_brazil_boleto
            },

            showBacenAlert: function(){
                return this.getGlobalConfig().show_bacen_alert;
            },

            showDocumentTypeField: function(){
                return false;
            },

            beforePlaceOrder: function(){
                if(this.validateForm()) {
                    if(validDocument(document.querySelector('#digitalhub_ebanx_brazil_boleto_document_number').value)){
                        this.placeOrder();
                    } else {
                        this.messageContainer.addErrorMessage({message: $t('Invalid Document')});
                    }
                }
            },

            getMask: function(){
                documentMask();
            },

            setLocalTotal: function (self) {
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
            
            validateForm: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },
        });
    }
);
