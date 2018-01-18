(function() {
  const loadIframe = () => {
    document.querySelector('#ebanx-voucher-frame').addEventListener('load', function() {
      const innerDoc = this.contentDocument || this.contentWindow.document;
      const voucherHeight = innerDoc.querySelector('body').offsetHeight + 40;
      const style = `
                width: 100%; 
                height: ${voucherHeight}px;
            `;
      this.style = style;

      document.querySelector('.loading-fading-circle').style = 'display: none;';
    });
  };

  loadIframe();
})();