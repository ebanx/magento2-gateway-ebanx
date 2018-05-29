define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalhub_ebanx_mexico_creditcard',
                component: 'DigitalHub_Ebanx/js/view/payment/mexico/method-renderer/creditcard'
            },
            {
                type: 'digitalhub_ebanx_mexico_oxxo',
                component: 'DigitalHub_Ebanx/js/view/payment/mexico/method-renderer/oxxo'
            },
            {
                type: 'digitalhub_ebanx_mexico_spei',
                component: 'DigitalHub_Ebanx/js/view/payment/mexico/method-renderer/spei'
            }
        );
        return Component.extend({});
    }
);
