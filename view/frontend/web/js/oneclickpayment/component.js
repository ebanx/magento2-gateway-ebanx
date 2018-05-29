define([
    'underscore',
    'uiComponent',
    'jquery',
    'DigitalHub_Ebanx/js/action/oneclickpayment/shipping-address',
    'DigitalHub_Ebanx/js/action/oneclickpayment/shipping-methods',
    'DigitalHub_Ebanx/js/action/oneclickpayment/payment-methods',
    'DigitalHub_Ebanx/js/action/oneclickpayment/init-quote',
    'DigitalHub_Ebanx/js/action/oneclickpayment/place-order',
    'DigitalHub_Ebanx/js/action/oneclickpayment/session-check',
    'DigitalHub_Ebanx/js/action/saved-cards',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Magento_Ui/js/modal/alert'
], function(
    _,
    Component,
    $,
    shippingAddress,
    shippingMethods,
    paymentMethods,
    initQuote,
    placeOrder,
    sessionCheck,
    savedCards,
    modal,
    $t,
    alert,
    customer
) {
    'use strict';
    return Component.extend({
        defaults: {
            oneClickPaymentModalWindow: '',
            cartId: '',
            shippingAddressList: [],
            shippingMethodList: [],
            savedCardsList: [],
            shippingAddressId: null,
            billingAddressId: null,
            shippingMethod: null,
            paymentMethod: null,
            orderIncrementId: null,
            successPage: false,
            useSavedCard: null
        },
        initObservable: function () {
            this._super()
                .observe([
                    'oneClickPaymentModalWindow',
                    'cartId',
                    'shippingAddressList',
                    'shippingMethodList',
                    'savedCardsList',
                    'shippingAddressId',
                    'billingAddressId',
                    'shippingMethod',
                    'paymentMethod',
                    'orderIncrementId',
                    'successPage',
                    'useSavedCard'
                ]);
            return this;
        },
        initialize: function () {
            this._super();
            var self = this;
        },
        setModalElement: function (element) {
            var self = this;

            $.when(sessionCheck()).done(function(result){
                if(result.loggedin){
                    $('.product-add-form').after('<button class="action primary" id="btn-oneclickpayment">1-Click Buy</button>');

                    if (!self.oneClickPaymentModalWindow()) {
                        self.oneClickPaymentModalWindow(element)

                        var options = {
                            'type': 'popup',
                            'modalClass': 'popup-oneclickpayment',
                            'responsive': true,
                            'innerScroll': true,
                            'buttons': []
                        };

                        $(self.oneClickPaymentModalWindow()).modal(options);

                        $('#btn-oneclickpayment').trigger('click');
                        $('#btn-oneclickpayment').click(function(){
                            if(self.validateProductOptions()){
                                self.initForm()
                                $(self.oneClickPaymentModalWindow()).modal('openModal');
                            } else {
                                alert({
                                    content: $t('Please, select all required product options')
                                })
                            }
                        })
                    }
                }
            })
        },
        validateProductOptions: function(){
            var has_errors = false;
            if($('form#product_addtocart_form input[name^=super_attribute]').length){
                $('form#product_addtocart_form input[name^=super_attribute]').each(function(){
                    if(!$(this).val()){
                        has_errors = true;
                    }
                })
            }
            return !has_errors
        },
        initForm: function(){
            var self = this;

            var super_attribute = []
            if(jQuery('form#product_addtocart_form input[name^=super_attribute]').length){
                jQuery('form#product_addtocart_form input[name^=super_attribute]').each(function(){
                    var item = {
                        attr_id: $(this).attr('name').replace(/\D+/g,''),
                        option_id: $(this).val()
                    }
                    super_attribute.push(item)
                })
            }

            var quoteData = {
                product_id: jQuery('form#product_addtocart_form input[name=product]').val(),
                super_attribute: super_attribute,
                product_qty: jQuery('form#product_addtocart_form input[name=qty]').val()
            }

            // populate shipping Address List
            $.when(shippingAddress()).done(function(result){
                var list = [
                    {label: '- Select -', value: ''}
                ];
                list = _.union(list, result.items)
                self.shippingAddressList(list)
            });

            // populate shipping method List when shipping address changes
            this.shippingAddressId.subscribe(function(shipping_address_id){
                self.shippingMethod(null)

                if(shipping_address_id){
                    // init quote with current product
                    $('body').loader('show');
                    $.when(initQuote(quoteData)).done(function(quote_result){
                        self.cartId(quote_result.cart_id)
                        $.when(shippingMethods(quote_result.cart_id, shipping_address_id)).done(function(result){
                            var list = [
                                {label: '- Select -', value: ''}
                            ];
                            list = _.union(list, result.items)
                            self.shippingMethodList(list)
                            $('body').loader('hide');
                        });
                    })
                }
            })

            this.shippingMethod.subscribe(function(shipping_method){
                self.useSavedCard(null)

                if(shipping_method){
                    // set shipping method and call payment methods available
                    var data = {cart_id: self.cartId(), shipping_method: shipping_method};
                    $('body').loader('show');
                    $.when(paymentMethods(data)).done(function(payment_methods_result){

                        var method = payment_methods_result.items && payment_methods_result.items[0] ? payment_methods_result.items[0].code : null;
                        self.paymentMethod(method);

                        // populate saved cards List
                        $.when(savedCards(method)).done(function(result){

                            var list = [
                                {label: '- Select -', value: ''}
                            ];

                            _.forEach(result.items, function(item){
                                list.push({
                                    label: item.payment_type_code + ' - ' + item.masked_card_number,
                                    value: item.id
                                })
                            })

                            self.savedCardsList(list)
                            $('body').loader('hide');
                        });
                    })
                }
            });
        },
        placeOrder: function(){
            var self = this;

            var orderData = {
                cart_id: this.cartId(),
                shipping_address_id: this.shippingAddressId(),
                shipping_method: this.shippingMethod(),
                payment_method: this.paymentMethod(),
                token_id: this.useSavedCard()
            }

            $('body').loader('show');
            $.when(placeOrder(orderData)).done(function(order_result){
                if(order_result.success){
                    self.success(order_result)
                } else {
                    if(order_result.message){
                        alert({
                            content: $t(order_result.message)
                        })
                    } else {
                        alert({
                            content: $t('An error ocurred when trying place your order.')
                        })
                    }
                }
                $('body').loader('hide');
            });
        },
        success: function(order_result){
            this.successPage(true)
            this.orderIncrementId(order_result.order_increment_id)
        }
    });
});
