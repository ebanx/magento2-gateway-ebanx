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
                template: 'Ebanx_Payments/payment/ebanx_tef',
                selectedBank: 'bradesco'
            },
            getData: function() {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'selected_bank': this.selectedBank
                    }
                };
            },
            setSelectedBank: function (selectedBank) {
                this.selectedBank = selectedBank;
            },
            beforePlaceOrder: function (data) {
                console.log(data);
                this.setSelectedBank(data.selectedBank);
                this.placeOrder();
            }
        });
    }
);
