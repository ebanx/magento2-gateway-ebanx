(function() {
  const url = document.querySelector('#redirectURL').value;

  setTimeout(() => {
    window.location.replace(url);
  }, 3000)
})();
