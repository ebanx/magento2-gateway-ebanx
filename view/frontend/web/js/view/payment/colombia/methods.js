define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalhub_ebanx_colombia_eft',
                component: 'DigitalHub_Ebanx/js/view/payment/colombia/method-renderer/eft'
            },
            {
                type: 'digitalhub_ebanx_colombia_baloto',
                component: 'DigitalHub_Ebanx/js/view/payment/colombia/method-renderer/baloto'
            },
            {
                type: 'digitalhub_ebanx_colombia_creditcard',
                component: 'DigitalHub_Ebanx/js/view/payment/colombia/method-renderer/creditcard'
            }
        );
        return Component.extend({});
    }
);
