(function() {
  const buildCreditCardForm = () => {
    var card = new Card({
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

  setTimeout(() => {
    const cardForm = document.querySelector("#card-form");

    buildCreditCardForm();
  }, 2000);
})();
