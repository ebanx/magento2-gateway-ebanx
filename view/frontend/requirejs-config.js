/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    paths: {
        'cc-br' : 'Ebanx_Payments/js/cc.min',
        'card-js' : 'https://cdnjs.cloudflare.com/ajax/libs/card/2.4.0/card.min',
        'lib-js' : 'https://js.ebanx.com/ebanx-1.5.min',
    },
    shim: {
        'cc-br': {
            deps: ['card-js', 'lib-js']
        }
    }
};
