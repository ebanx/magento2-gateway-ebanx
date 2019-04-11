define(
    [
        'jquery',
        'jquery_mask'
    ],
    function ($, jquery_mask) {
        'use strict';
        return function (credit_card_number_element) {
            var options =  {
                onKeyPress: function(credit_card_number, e, field, options) {
                    var masks = ['9999 999999 99999', '9999 9999 9999 9999'];
                    var mask = (credit_card_number.length > 15) ? masks[1] : masks[0];
                    $(credit_card_number_element).mask(mask);
                }};
            $(credit_card_number_element).mask('9999 999999 99999', options);
        };
    }
);
