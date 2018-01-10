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

        // Brazil
        rendererList.push(
            {
                type: 'ebanx_boleto',
                component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-boleto'
            }
        );
        rendererList.push(
            {
                type: 'ebanx_tef',
                component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-tef'

            }
        );
        rendererList.push(
            {
                type: 'ebanx_wallet',
                component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-wallet'
            }
        );
        rendererList.push(
            {
                type: 'ebanx_creditcard',
                component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-creditcard'
            }
        );

        // Mexico
        rendererList.push(
            {
                type: 'ebanx_oxxo',
                component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-oxxo'
            }
        );
        rendererList.push(
            {
                type: 'ebanx_spei',
                component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-spei'
            }
        );

      // Chile
      rendererList.push(
        {
          type: 'ebanx_sencillito',
          component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-sencillito'
        }
      );
      rendererList.push(
        {
          type: 'ebanx_servipag',
          component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-servipag'
        }
      );
      rendererList.push(
        {
          type: 'ebanx_multicaja',
          component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-multicaja'
        }
      );
      rendererList.push(
        {
          type: 'ebanx_webpay',
          component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-webpay'
        }
      );

      // Peru
      rendererList.push(
        {
          type: 'ebanx_pagoefectivo',
          component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-pagoefectivo'
        }
      );
      rendererList.push(
        {
          type: 'ebanx_safetypay',
          component: 'Ebanx_Payments/js/view/payment/method-renderer/ebanx-safetypay'
        }
      );

        return Component.extend({});
    }
);
