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
            },
            {
                type: 'digitalhub_ebanx_argentina_rapipago',
                component: 'DigitalHub_Ebanx/js/view/payment/argentina/method-renderer/rapipago'
            },
            {
                type: 'digitalhub_ebanx_argentina_pagofacil',
                component: 'DigitalHub_Ebanx/js/view/payment/argentina/method-renderer/pagofacil'
            },
            {
                type: 'digitalhub_ebanx_argentina_cupondepagos',
                component: 'DigitalHub_Ebanx/js/view/payment/argentina/method-renderer/cupondepagos'
            }
        );
        return Component.extend({});
    }
);
