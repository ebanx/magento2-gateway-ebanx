define([
  'vanilla-masker',
], function (VMasker) {
  const getElementWhenDone = (selector, callback) => {
    const interval = setInterval(function () {
      const element = document.querySelector(selector);
      if (!element)
        return;

      clearInterval(interval);
      callback(element);
    }, 300);
  };

  return (inputSelector) => {
    getElementWhenDone(inputSelector, (element) => {
      VMasker(element).maskPattern('999.999.999-99');
    });
  };
});
