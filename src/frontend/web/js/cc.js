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
                form: "#card-form",
                container: ".card",
                width: 275,
                placeholders: {
                    number: "•••• •••• •••• ••••",
                    expiry: "••/••••",
                    cvc: "•••"
                }
            });
        };

        const createInstalment = (paymentTerms) => {
            let options = ``;
            let localAmount;

            for(let term of paymentTerms){
                localAmount = parseFloat(Math.round(term.localAmountWithTax*100)/100).toFixed(2);
                options += `<option value="${term.instalmentNumber}" data-local-amount="${localAmount}">${term.instalmentNumber}x de R$${localAmount} ${term.hasInterest ? 'com juros' : ''} </option>`
            }

            document.querySelector("#instalment-cc-br").innerHTML = `${options}`;
        };

        waitFor(function(){
            return document.querySelector('#card-form');
        }, buildCreditCardForm);

        return {
            createInstalment: createInstalment
        };
    }
);
