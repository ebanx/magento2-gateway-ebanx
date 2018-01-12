/*browser:true*/
/*global define*/
define(
  [
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'lib-js',
    'document-mask',
    'cc-util',
    'Magento_Checkout/js/model/quote',
    'cc-br'
  ],
  function(Component, $, EBANX, documentMask, util, quote, cc) {
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
        paymentDocument: window.checkoutConfig.payment.ebanx.customerDocument,
        totals: quote.getTotals()
      },
      initialize: function() {
        this._super();
        documentMask('#ebanx_creditcard_document');
        this.totals.subscribe(util.onUpdateTotalsAndInstalments, this);
        util.onUpdateTotalsAndInstalments(this.totals.peek().grand_total, 'Brazil');
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
      setCardData: function(cardData, paymentDocument) {
        this.brand = cardData.payment_type_code;
        this.token = cardData.token;
        this.paymentDocument = paymentDocument;

        this.placeOrder();
      },
      beforePlaceOrder: function(data) {
        var self = this;
        util
          .tokenize(data, 'br')
          .then(function(res) {
            if (res !== false) {
              self.setCardData(res, data.paymentDocument);
            }
          })
          .catch(function(err) {
            util.showAlertMessage(err, 'Atenção: ');
          });
      }
    });
  }
);
