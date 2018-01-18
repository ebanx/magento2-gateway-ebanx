/*jshint browser:true jquery:true*/
/*global alert*/

var config = {
  paths: {
    'cc-br': 'Ebanx_Payments/js/cc.min',
    'card-js': 'https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min',
    'lib-js': 'https://js.ebanx.com/ebanx-1.5.min',
    'document-mask': 'Ebanx_Payments/js/document-mask.min',
    'vanilla-masker': 'Ebanx_Payments/js/lib/vanilla-masker.min',
    'cc-util': 'Ebanx_Payments/js/util/cc-util.min',
    'wait-for': 'Ebanx_Payments/js/util/wait-for.min',
  },
  shim: {
    'cc-br': {
      deps: ['card-js', 'lib-js', 'cc-util'],
    },
    'cc-util': {
      deps: ['lib-js'],
    },
  },
};