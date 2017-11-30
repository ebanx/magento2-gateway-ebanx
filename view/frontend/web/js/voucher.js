(function(){
    const loadIframe = () => {
        document.querySelector('#ebanx-boleto-frame').addEventListener("load", function() {
            const innerDoc = this.contentDocument || this.contentWindow.document;
            const customBlock = innerDoc.querySelector('.ebanx-boleto .custom');
            const masterBlock = innerDoc.querySelector('.ebanx-boleto .mestre');
            const boletoHeight = (customBlock.offsetHeight + masterBlock.offsetHeight) - this.offsetTop;
            console.log(boletoHeight);
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