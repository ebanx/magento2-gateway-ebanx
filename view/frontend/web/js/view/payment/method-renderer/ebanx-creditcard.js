/*browser:true*/
/*global define*/
define(
  [
    "Magento_Checkout/js/view/payment/default",
    "jquery",
    "lib-js",
    "card-js",
    "cc-br"
  ],
  function(Component, $, EBANX) {
    "use strict";

    window.EBANX = EBANX;

    return Component.extend({
      defaults: {
        template: "Ebanx_Payments/payment/ebanx_creditcard_br",
        brand: null,
        cvv: null,
        instalments: 1,
        number: null,
        expiry: null,
        token: null,
      },
      getData: function() {
        return {
          method: this.getCode(),
          additional_data: {
            brand: this.brand,
            cvv: this.cvv,
            instalments: this.instalments,
            token: this.token,
          }
        };
      },
      setCardData: function(data) {
        this.brand = data.payment_type_code;
        this.token = data.token;

        this.placeOrder();
      },
      beforePlaceOrder: function(data) {
        if (!this.validateForm("#card-form")) {
          return null;
        }

        this.tokenizer({
          card_number: data.number.replace(/ /g, ""),
          card_due_date: data.expiry.replace(/ /g, ""),
          card_cvv: data.cvv,
        });

      },
      tokenizer: function(param) {
        EBANX.config.setMode("test");
        EBANX.config.setPublishableKey("pk_1231000");
        EBANX.config.setCountry("br");

        var createTokenCallback = function(ebanxResponse) {
          if (ebanxResponse.data.hasOwnProperty("status")) {
            this.setCardData(ebanxResponse.data);
          } else {
            var errorMessage =
              ebanxResponse.error.err.status_message ||
              ebanxResponse.error.err.message;
            console.error(errorMessage);
          }
        }.bind(this);

        EBANX.card.createToken(
          {
            card_number: param.card_number,
            card_name: "Magento testes",
            card_due_date: param.card_due_date,
            card_cvv: param.card_cvv,
          },
          createTokenCallback
        );
      },
      validateForm: function(form) {
        return $(form).validation() && $(form).validation("isValid");
      },
    });
  }
);
