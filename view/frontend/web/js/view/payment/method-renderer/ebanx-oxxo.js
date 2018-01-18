/*browser:true*/
/*global define*/
define(
  [
    'Magento_Checkout/js/view/payment/default',
  ],
  function (Component) {
    'use strict';
    return Component.extend({
      defaults: {
        template: 'Ebanx_Payments/payment/ebanx_oxxo',
      },
    });
  }
);
