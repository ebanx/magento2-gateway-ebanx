/*browser:true*/
/*global define*/
define(
    [
        'underscore',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Payment/js/view/payment/cc-form',
        'Ebanx_Payments/js/action/place-order',
        'mage/translate',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function (_, $, quote, Component, placeOrderAction, $t, additionalValidators) {
        'use strict';
        var billingAddress = quote.billingAddress();
        return Component.extend({
            self: this,
            defaults: {
                template: 'Ebanx_Payments/payment/boleto-form',
                firstname: billingAddress.firstname,
                lastname: billingAddress.lastname
            },
            initObservable: function () {
                this._super()
                    .observe([
                        'socialSecurityNumber',
                        'boletoType',
                        'firstname',
                        'lastname'
                    ]);
                return this;
            },
            setPlaceOrderHandler: function(handler) {
                this.placeOrderHandler = handler;
            },
            setValidateHandler: function(handler) {
                this.validateHandler = handler;
            },
            getCode: function() {
                return 'ebanx_boleto';
            },
            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'social_security_number': this.socialSecurityNumber(),
                        'boleto_type': this.boletoType(),
                        'firstname': this.firstname(),
                        'lastname': this.lastname()
                    }
                };
            },
            isActive: function() {
                return true;
            },
            /**
             * @override
             */
            placeOrder: function(data, event) {
                var self = this,
                    placeOrder;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), this.redirectAfterPlaceOrder);

                    $.when(placeOrder).fail(function(response) {
                        self.isPlaceOrderActionAllowed(true);
                    });
                    return true;
                }
                return false;
            },
            getControllerName: function() {
                return window.checkoutConfig.payment.iframe.controllerName[this.getCode()];
            },
            getPlaceOrderUrl: function() {
                return window.checkoutConfig.payment.iframe.placeOrderUrl[this.getCode()];
            },
            context: function() {
                return this;
            },
            validate: function () {
                var form = 'form[data-role=ebanx-boleto-form]';

                var validate =  $(form).validation() && $(form).validation('isValid');

                if(!validate) {
                    return false;
                }

                return true;
            },
            showLogo: function() {
                return window.checkoutConfig.payment.ebanx.showLogo;
            },
            getBoletoTypes: function() {
                return _.map(window.checkoutConfig.payment.ebanxBoleto.boletoTypes, function(value, key) {
                    return {
                        'key': value.value,
                        'value': value.label
                    }
                });
            }
        });
    }
);
