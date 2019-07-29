define(
    [
        'jquery',
        'jquery_mask'
    ],
    function ($, jquery_mask) {
        'use strict';
        return function () {
            var options =  {
                onKeyPress: function(cpf_document, e, field, options) {
                    var masks = ['000.000.000-000', '00.000.000/0000-00'];
                    var mask = (cpf_document.length > 14) ? masks[1] : masks[0];
                    $('.masked-document').mask(mask, options);
                }};

            $('.masked-document').length > 11
                ? $('.masked-document').mask('00.000.000/0000-00', options) 
                : $('.masked-document').mask('000.000.000-00#', options);    
        };
    }
);
