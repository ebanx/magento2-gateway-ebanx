(function(){
   const redirect = () => {
      const redirectUrl = document.querySelector('#redirectURL').value;
      window.open(redirectUrl);
   }
   redirect();
})();
