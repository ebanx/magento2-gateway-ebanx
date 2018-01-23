define(
  [],
  function() {
    return (elementFinder, callback) => {
      const waiter = setInterval(() => {
        const element = elementFinder();
        if (typeof element === 'undefined' || element === null) {
          return;
        }
        clearInterval(waiter);
        callback(element);
      }, 500);
    };
  }
);
