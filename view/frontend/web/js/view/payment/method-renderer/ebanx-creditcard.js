/*browser:true*/
/*global define*/
define(
  [
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'lib-js',
    'document-mask',
    'cc-util',
    'card-js',
    'cc-br'
  ],
  function(Component, $, EBANX, documentMask, util) {
    'use strict';

    window.EBANX = EBANX;

    return Component.extend({
      defaults: {
        template: 'Ebanx_Payments/payment/ebanx_creditcard_br',
        brand: null,
        cvv: null,
        instalments: 1,
        number: null,
        expiry: null,
        token: null,
        paymentDocument: window.checkoutConfig.payment.ebanx.customerDocument
      },
      initialize: function() {
        this._super();
        documentMask('#ebanx_creditcard_document');
      },
      getData: function() {
        return {
          method: this.getCode(),
          additional_data: {
            brand: this.brand,
            cvv: this.cvv,
            instalments: this.instalments,
            token: this.token,
            document: this.paymentDocument
          }
        };
      },
      setCardData: function(data) {
        this.brand = data.payment_type_code;
        this.token = data.token;
        this.placeOrder();
      },
      setDocument: function(paymentDocument) {
        this.paymentDocument = paymentDocument;
      },
      beforePlaceOrder: function(data) {
        var self = this;

        self.setDocument(data.paymentDocument);

        util
          .tokenize(data, 'br')
          .then(function(res) {
              if(res !== false){
                self.setCardData(res);
              }
          })
          .catch(function(err) {
            util.showAlertMessage(err, 'Atenção: ');
          });
      }
    });
  }
);
