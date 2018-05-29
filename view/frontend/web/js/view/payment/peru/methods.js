define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalhub_ebanx_peru_pagoefectivo',
                component: 'DigitalHub_Ebanx/js/view/payment/peru/method-renderer/pagoefectivo'
            },
            {
                type: 'digitalhub_ebanx_peru_safetypay',
                component: 'DigitalHub_Ebanx/js/view/payment/peru/method-renderer/safetypay'
            }
        );
        return Component.extend({});
    }
);
