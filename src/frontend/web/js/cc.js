/*browser:true*/
/*global define*/
/*global Card*/
define(
    [
        'wait-for',
        'card-js',
    ],
    function(waitFor) {
        const buildCreditCardForm = () => {
            new Card({
                form: '#card-form',
                container: '.card',
                width: 275,
                placeholders: {
                    number: '•••• •••• •••• ••••',
                    expiry: '••/••',
                    cvc: '•••'
                }
            });
        };

        waitFor(function(){
            return document.querySelector('#card-form');
        }, buildCreditCardForm);
    }
);
