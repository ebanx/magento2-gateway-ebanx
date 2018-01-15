(function() {
  const url = document.querySelector('#redirectURL').value;
  const newWinOrTab = window.open(url, "_blank");      
  newWinOrTab.focus();
})();
