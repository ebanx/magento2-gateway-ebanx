/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    paths: {
        'cc-br' : 'Ebanx_Payments/js/cc.min',
        'eft' : 'Ebanx_Payments/js/eft.min',
        'card-js' : 'https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min',
        'lib-js' : 'https://js.ebanx.com/ebanx-1.5.min',
        'document-mask': 'Ebanx_Payments/js/document-mask.min',
        'vanilla-masker': 'Ebanx_Payments/js/lib/vanilla-masker.min',
        'wait-for': 'Ebanx_Payments/js/wait-for.min'
    },
    shim: {
        'cc-br': {
            deps: ['card-js', 'lib-js']
        }
    }
};
