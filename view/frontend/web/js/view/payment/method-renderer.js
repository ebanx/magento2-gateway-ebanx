/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'ebanx_boleto',
                component: 'Ebanx_PaymentGateway/js/view/payment/method-renderer/brazil-boleto'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
