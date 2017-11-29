(function(){
    const loadIframe = () => {
        document.querySelector('#ebanx-boleto-frame').addEventListener("load", function() {
            const innerDoc = this.contentDocument || this.contentWindow.document;
            const boletoHeight = (innerDoc.querySelector('.ebanx-boleto').clientHeight) - 500;
            const style = `
                width: 100%; 
                border: 0px;
                height: ${boletoHeight}px;
                visibility: visible;
            `;
            this.style = style;

            document.querySelector(".loading-fading-circle").style = "display: none;";
        }, true);
    }

    loadIframe();
})();