define(
    [
        'mage/storage',
        'mage/url'
    ],
    function (storage, url) {
        return function () {
            return storage.post(url.build('digitalhub_ebanx/checkout/documentverification'), false)
        };
    }
);
