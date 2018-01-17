/*browser:true*/
/*global define*/
define(
  [
    'wait-for',
  ],
  function (waitFor) {
    const addBanksToElement = (banks, element) => {
      let options = ``;

      for (let bank in banks) {
        options += `<option value="${bank}">${banks[bank]}</option>`
      }

      element.innerHTML = `${options}`;
    };

    const populateBankSelectWithBanks = (selector, banks) => {
      waitFor(
        () => document.querySelector(selector),
        (element) => {
          addBanksToElement(banks, element);
        }
      );
    };

    return {
      populateBankSelectWithBanks: populateBankSelectWithBanks
    };
  }
);
