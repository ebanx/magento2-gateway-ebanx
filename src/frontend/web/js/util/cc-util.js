define(
  [
    'lib-js',
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/url',
    'wait-for',
  ],
  function(EBANX, $, alert, url, waitFor) {
    'use strict';

    const formatDueDate = expiry => {
      const dueDateSplited = expiry.replace(/ /g, '').split('/');
      const dueDate = dueDateSplited[0] + '/20' + dueDateSplited[1];
      return dueDate;
    };

    const showAlertMessage = (errorMessage, title) => {
      alert({
        title: title,
        content: errorMessage,
      });
    };

    const disableBtnPlaceOrder = () => {
      $('#btn_cc_br_form_place_order').attr('disabled', 'disabled');
    };

    const enableBtnPlaceOrder = () => {
      $('#btn_cc_br_form_place_order').removeAttr('disabled');
    };

    const validateForm = (form) => {
      return $(form).validation() && $(form).validation('isValid');
    };

    const tokenize = ({
      number,
      expiry,
      cvv,
    }, country) => {
      disableBtnPlaceOrder();

      if (!validateForm('#card-form')) {
        enableBtnPlaceOrder();
        return new Promise((resolve) => resolve(false));
      }

      const card_number = number.replace(/ /g, '');
      const card_due_date = formatDueDate(expiry);
      const card_cvv = cvv.replace(/ /g, '');

      return new Promise((resolve, reject) => {
        EBANX.config.setMode(window.checkoutConfig.payment.ebanx.mode);
        EBANX.config.setPublishableKey(window.checkoutConfig.payment.ebanx.publicKey);
        EBANX.config.setCountry(country);

        const createTokenCallback = ebanxResponse => {
          enableBtnPlaceOrder();
          if (ebanxResponse.data && ebanxResponse.data.status === 'SUCCESS') {
            resolve(ebanxResponse.data);
          } else {
            const errorMessage = ebanxResponse.error.err.message || ebanxResponse.error.err.status_message;
            reject(errorMessage);
          }
        };

        EBANX.card.createToken({
          card_number: card_number,
          card_name: 'Magento Credit Card',
          card_due_date: card_due_date,
          card_cvv: card_cvv,
        }, createTokenCallback);
      });
    };

    const createInstalment = paymentTerms => {
      let options = '';
      let localAmount;

      for (let term of paymentTerms) {
        localAmount = parseFloat(Math.round(term.localAmountWithTax * 100) / 100).toFixed(2);
        options += `<option value='${term.instalmentNumber}' data-local-amount='${localAmount}'>${term.instalmentNumber}x de R$${localAmount} ${term.hasInterest ? 'com juros' : ''} </option>`;
      };

      waitFor(() => {
        return document.querySelector('#instalment-cc-br');
      }, (instalmentSelector) => {
        instalmentSelector.innerHTML = `${options}`;
      });
    };

    const onUpdateTotalsAndInstalments = (grand_total, country) => {
      $.post(
        url.build('ebanx/payment/instalmentterms'), {
          country: country,
          amount: grand_total,
        },
        'json'
      ).done((responsePaymentTerms) => {
        createInstalment(responsePaymentTerms);
      });
    };

    return {
      formatDueDate: formatDueDate,
      showAlertMessage: showAlertMessage,
      disableBtnPlaceOrder: disableBtnPlaceOrder,
      enableBtnPlaceOrder: enableBtnPlaceOrder,
      tokenize: tokenize,
      createInstalment: createInstalment,
      onUpdateTotalsAndInstalments: onUpdateTotalsAndInstalments,
    };
  });
