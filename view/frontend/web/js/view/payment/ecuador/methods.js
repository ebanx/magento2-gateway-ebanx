define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalhub_ebanx_ecuador_safetypay',
                component: 'DigitalHub_Ebanx/js/view/payment/ecuador/method-renderer/safetypay'
            }
        );
        return Component.extend({});
    }
);
