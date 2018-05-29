require([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function($, confirm) {
    $('.remove-saved-card').show().click(function(e){
        e.preventDefault();
        var el = this;
        confirm({
            content: 'Are you sure you want to delete the card information?',
            actions: {
                confirm: function(){
                    window.location = $(el).attr('data-url');
                },
                cancel: function(){},
                always: function(){}
            }
        });
    });
});
