define(
    function () {
        'use strict';
        return function (document_text) {
            return(document_text.length === 8 || document_text.length === 9);
        };
    }
);
