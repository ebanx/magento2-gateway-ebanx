/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery'
    ],
    function ($) {
        'use strict';
        var interval = setInterval(function () {
            if (typeof $.validator === 'undefined')
                return;

            clearInterval(interval);

            $.validator.addMethod(
                'document-validator',
                function (document, item) {
                    return false;
                },
                'CPF inválido'
            );
        }, 500);
    }
);
