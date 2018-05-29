define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalhub_ebanx_chile_servipag',
                component: 'DigitalHub_Ebanx/js/view/payment/chile/method-renderer/servipag'
            },
            {
                type: 'digitalhub_ebanx_chile_sencillito',
                component: 'DigitalHub_Ebanx/js/view/payment/chile/method-renderer/sencillito'
            },
            {
                type: 'digitalhub_ebanx_chile_webpay',
                component: 'DigitalHub_Ebanx/js/view/payment/chile/method-renderer/webpay'
            },
            {
                type: 'digitalhub_ebanx_chile_multicaja',
                component: 'DigitalHub_Ebanx/js/view/payment/chile/method-renderer/multicaja'
            }
        );
        return Component.extend({});
    }
);
