define(
    function () {
        'use strict';
        return function (document_text) {
            return(2 <= document_text.length && document_text.length <= 10);
        };
    }
);
