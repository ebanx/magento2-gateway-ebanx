(function(){
    const loadIframe = () => {
        document.querySelector('#ebanx-boleto-frame').addEventListener('load', function() {
            const innerDoc = this.contentDocument || this.contentWindow.document;
            const boletoHeight = innerDoc.querySelector('.ebanx-boleto').offsetHeight + 40;
            const style = `
                width: 100%; 
                height: ${boletoHeight}px;
            `;
            this.style = style;

            document.querySelector('.loading-fading-circle').style = 'display: none;';
        });
    }

    loadIframe();
})();
