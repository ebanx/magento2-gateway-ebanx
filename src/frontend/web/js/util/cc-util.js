define(
  [
    'lib-js', 
    'jquery', 
    'Magento_Ui/js/modal/alert'
  ], 
  function(EBANX, $, alert) {
    'use strict';

    const formatDueDate = expiry => {
        const dueDateSplited = expiry.replace(/ /g, '').split('/');
        const dueDate = dueDateSplited[0] + '/20' + dueDateSplited[1];
        return dueDate;
    }

    const showAlertMessage = (errorMessage, title) => {
        alert({
        title: title,
        content: errorMessage,
        actions: {
            always: function() {}
        }
        });
    }

    const disableBtnPlaceOrder = () => {
        $('#btn_cc_br_form_place_order').attr('disabled', 'disabled');
    }

    const enableBtnPlaceOrder = () => {
        $('#btn_cc_br_form_place_order').removeAttr('disabled');
    }

    const validateForm = (form) => {
        return $(form).validation() && $(form).validation('isValid');
    }

    const tokenize = ({ number, expiry, cvv }, country) => {
        disableBtnPlaceOrder();

        if (!validateForm('#card-form')) {
          enableBtnPlaceOrder();
          return new Promise((resolve, reject) => resolve(false));
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

            EBANX.card.createToken(
                {
                card_number: card_number,
                card_name: 'Magento Credit Card',
                card_due_date: card_due_date,
                card_cvv: card_cvv
                },
                createTokenCallback
            );
        });
    }

    return {
        formatDueDate: formatDueDate,
        showAlertMessage: showAlertMessage,
        tokenize: tokenize,
        disableBtnPlaceOrder: disableBtnPlaceOrder,
        enableBtnPlaceOrder: enableBtnPlaceOrder,
    };
});
