(function(){
  console.log('caraca muleque');
  document.addEventListener("DOMContentLoaded", function() {
    console.log('que dia que isso');
    console.log(document.querySelector('#ebanx_boleto_document'));
    VMasker(document.querySelector('#ebanx_boleto_document')).maskMoney();
  });
})();
