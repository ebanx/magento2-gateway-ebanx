/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'wait-for'
    ],
    function ($, waitFor) {
        'use strict';
        waitFor(function(){return $.validator;}, function (validator) {
            validator.addMethod(
                'document-validator',
                function (document) {
                    document = document.replace(/[^\d]+/g,'');

                    if(document === ''){
                        return false;
                    }

                    if (document.length !== 11
                    || document === "00000000000"
                    || document === "11111111111"
                    || document === "22222222222"
                    || document === "33333333333"
                    || document === "44444444444"
                    || document === "55555555555"
                    || document === "66666666666"
                    || document === "77777777777"
                    || document === "88888888888"
                    || document === "99999999999"){
                        return false;
                    }

                    var digitSum = 0;
                    for (var i = 0; i < 9; i ++){
                        digitSum += parseInt(document.charAt(i)) * (10 - i);
                    }

                    var firstVerifyingDigit = 11 - (digitSum % 11);
                    if (firstVerifyingDigit > 9){
                        firstVerifyingDigit = 0;
                    }

                    if (firstVerifyingDigit !== parseInt(document.charAt(9))) {
                        return false;
                    }

                    digitSum = 0;
                    for (i = 0; i < 10; i ++){
                        digitSum += parseInt(document.charAt(i)) * (11 - i);
                    }

                    var secondVerifyingDigit = 11 - (digitSum % 11);
                    if (secondVerifyingDigit > 9){
                        secondVerifyingDigit = 0;
                    }
                    return secondVerifyingDigit === parseInt(document.charAt(10));
                },
                'CPF inválido'
            );
        });
    }
);
