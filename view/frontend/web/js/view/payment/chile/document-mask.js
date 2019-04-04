define(
    [
        'jquery',
        'jquery_mask'
    ],
    function ($, jquery_mask) {
        'use strict';
        return function () {
            $('.masked-document').mask('999999999');
        };
    }
);
