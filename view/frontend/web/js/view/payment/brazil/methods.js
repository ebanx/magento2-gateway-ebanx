define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalhub_ebanx_brazil_creditcard',
                component: 'DigitalHub_Ebanx/js/view/payment/brazil/method-renderer/creditcard'
            },
            {
                type: 'digitalhub_ebanx_brazil_boleto',
                component: 'DigitalHub_Ebanx/js/view/payment/brazil/method-renderer/boleto'
            },
            {
                type: 'digitalhub_ebanx_brazil_tef',
                component: 'DigitalHub_Ebanx/js/view/payment/brazil/method-renderer/tef'
            },
            {
                type: 'digitalhub_ebanx_brazil_ebanxaccount',
                component: 'DigitalHub_Ebanx/js/view/payment/brazil/method-renderer/ebanxaccount'
            }
        );
        return Component.extend({});
    }
);
