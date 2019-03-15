define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';
        rendererList.push(
            {
                type: 'digitalhub_ebanx_argentina_creditcard',
                component: 'DigitalHub_Ebanx/js/view/payment/argentina/method-renderer/creditcard'
            }
        );
        return Component.extend({});
    }
);
