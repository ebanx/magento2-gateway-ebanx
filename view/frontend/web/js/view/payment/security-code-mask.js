define(
    [
        'jquery',
        'jquery_mask'
    ],
    function ($, jquery_mask) {
        'use strict';
        return function (security_code_element) {
            $(security_code_element).mask('9999');
        };
    }
);
