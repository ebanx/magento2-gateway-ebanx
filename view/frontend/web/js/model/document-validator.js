/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';

        $.validator.addMethod([
            function (document, item) {
                return false;
            },
            'CPF inválido'
        ]);
    }
);
