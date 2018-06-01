define(
    [
        'underscore',
        'ebanx',
        'DigitalHub_Ebanx/js/action/installments',
        'DigitalHub_Ebanx/js/action/document-number-verification',
        'DigitalHub_Ebanx/js/action/saved-cards',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator',
        'mage/translate',
        'jquery',
        'DigitalHub_Ebanx/js/action/total-local-currency'
    ],
    function (
        _,
        EBANX,
        installments,
        documentNumberVerification,
        savedCards,
        Component,
        checkoutData,
        quote,
        priceUtils,
        fullScreenLoader,
        additionalValidators,
        creditCardData,
        cardNumberValidator,
        $t,
        $,
        totalLocalCurrency
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'DigitalHub_Ebanx/payment/base-form',
                totalLocalCurrency: '',
                useSavedCc: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardHolderName: '',
                creditCardVerificationNumber: '',
                creditCardToken: '',
                documentNumber: '',
                showDocumentFields: false,
                saveCc: 0,
                showSavedCardsField: false,
                availableInstallments: [],
                availableSavedCc: [],
                maskedCreditCardNumber: '',
                paymentTypeCode: ''
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'totalLocalCurrency',
                        'useSavedCc',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardHolderName',
                        'creditCardVerificationNumber',
                        'creditCardToken',
                        'creditCardInstallments',
                        'maskedCreditCardNumber',
                        'availableInstallments',
                        'availableSavedCc',
                        'showSavedCardsField',
                        'paymentTypeCode',
                        'documentNumber',
                        'showDocumentFields',
                        'saveCc',
                        'validCreditCardData'
                    ]);
                return this;
            },

            initialize: function() {
                var self = this;
                this._super();

                var mode = this.getGlobalConfig().sandbox ? 'test' : 'production';
                window.EBANX = EBANX;
                EBANX.config.setMode(mode);
                EBANX.config.setCountry(quote.billingAddress().countryId.toLowerCase());
                EBANX.config.setPublishableKey(this.getGlobalConfig().public_integration_key);

                // document number verification promise
                $.when(documentNumberVerification()).done(function (result) {
                    self.showDocumentFields(!result.has_document_number)
                });

                // installments promise
                $.when(installments()).done(function (result) {

                    var installmentsOptions = []

                    _.forEach(result.installments, function(item){
                        var label = item.number + $t('x of ') + priceUtils.formatPrice(item.installment_value, quote.getPriceFormat());
                        if(item.interest){
                            label+= $t(' (with interest)')
                        }
                        installmentsOptions.push({
                            label: label,
                            value: item.number,
                            interest: item.interest
                        })
                    })

                    self.availableInstallments(installmentsOptions)

                }).always(function () {
                    // after all
                });

                // saved cards promise
                $.when(savedCards(this.getCode())).done(function (result) {

                    var savedCardsOptions = []

                    _.forEach(result.items, function(item){
                        savedCardsOptions.push({
                            label: item.masked_card_number + ' ('+item.payment_type_code+')',
                            value: item.id
                        })
                    })

                    if(savedCardsOptions.length){
                        self.showSavedCardsField(true)
                    }

                    savedCardsOptions.push({
                        label: $t('Use a different credit card'),
                        value: 'new'
                    })

                    self.availableSavedCc(savedCardsOptions)

                }).always(function () {
                    // after all
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
                return 'DigitalHub_Ebanx/payment/brazil/creditcard/form'
            },

            getData: function() {
                if(this.useSavedCc() == 'new' || !this.showSavedCardsField()){
                    return {
                        method: this.getCode(),
                        additional_data: {
                            'token': this.creditCardToken(),
                            'cvv': this.creditCardVerificationNumber(),
                            'masked_card_number': this.maskedCreditCardNumber(),
                            'payment_type_code': this.paymentTypeCode(),
                            'installments': this.creditCardInstallments(),
                            'document_number': this.documentNumber(),
                            'save_cc': this.saveCc()
                        }
                    };
                } else {
                    return {
                        method: this.getCode(),
                        additional_data: {
                            'use_saved_cc': this.useSavedCc(),
                            'installments': this.creditCardInstallments(),
                            'document_number': this.documentNumber()
                        }
                    };
                }
            },

            isActive: function () {
                return true;
            },

            getCcMonths: function() {
                var months = [];
                for(var i = 1; i<=12; i++){
                    months.push({key: (i < 10 ? '0' + i : i), label: (i < 10 ? '0' + i : i)});
                }
                return months;
            },

            getCcYears: function() {
                var years = [];
                var date = new Date();
                var max_year = parseInt(date.getFullYear()) + 20;
                for(var i = parseInt(date.getFullYear()); i<=max_year; i++){
                    years.push({key: i, label: i});
                }
                return years;
            },

            getInstallments: function(){
                return this.availableInstallments()
            },

            getSavedCcList: function(){
                return this.availableSavedCc()
            },

            canSaveCc: function(){
                return this.getGlobalConfig().can_save_cc
                    && window.checkoutConfig.quoteData.customer_id
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
                return window.checkoutConfig.payment.digitalhub_ebanx_brazil_creditcard
            },

            showDocumentTypeField: function(){
                return false;
            },

            _createToken: function(callback){
                EBANX.card.createToken({
                  card_number: this.creditCardNumber(),
                  card_name: this.creditCardHolderName(),
                  card_due_date: this.creditCardExpMonth() + '/' + this.creditCardExpYear(),
                  card_cvv: this.creditCardVerificationNumber()
                }, callback);
            },

            validateCreditCardData: function(){

                var ccNumberResult = cardNumberValidator(this.creditCardNumber());
                var ccVerificationNumber = this.creditCardVerificationNumber();
                var ccHolderName = this.creditCardHolderName();
                var ccExpMonth = this.creditCardExpMonth();
                var ccExpYear = this.creditCardExpYear();

                var has_errors = false;

                if(ccNumberResult.isValid){
                    if(ccVerificationNumber.length != ccNumberResult.card.code.size){
                        has_errors = true;
                    }
                    if(!ccHolderName.length){
                        has_errors = true;
                    }
                    if(!ccExpMonth || !ccExpYear){
                        has_errors = true;
                    }
                } else {
                    has_errors = true;
                }

                return !has_errors;
            },

            beforePlaceOrder: function(){
                // validate and tokenize card before to send
                var _this = this;

                // validate default form
                if(this.validate()){
                    if(this.useSavedCc() == 'new' || !this.showSavedCardsField()){
                        if(this.validateCreditCardData()){
                            fullScreenLoader.startLoader();
                            this._createToken(function(ebanxResponse){
                                if (ebanxResponse.data.token) {
                                    _this.creditCardToken(ebanxResponse.data.token);
                                    _this.paymentTypeCode(ebanxResponse.data.payment_type_code);
                                    _this.maskedCreditCardNumber(ebanxResponse.data.masked_card_number);
                                    // _this.messageContainer.addSuccessMessage({message: 'Token generation success!'})
                                    _this.placeOrder();
                                } else {
                                    _this.creditCardToken = null
                                    _this.messageContainer.addErrorMessage({message: $t('Token generation error. Please contact support.')});
                                }
                                fullScreenLoader.stopLoader();
                            })
                        } else {
                            this.messageContainer.addErrorMessage({message: $t('Invalid Credit Card Data')});
                        }
                    } else {
                        _this.placeOrder();
                    }
                }
            }
        });
    }
);
