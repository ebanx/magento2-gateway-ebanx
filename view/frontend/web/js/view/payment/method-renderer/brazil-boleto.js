/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Ebanx_PaymentGateway/payment/form',
                transactionResult: ''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult'
                    ]);
                return this;
            },

            getCode: function() {
                return 'ebanx_boleto';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        // 'transaction_result': this.transactionResult()
                    }
                };
            }
        });
    }
);