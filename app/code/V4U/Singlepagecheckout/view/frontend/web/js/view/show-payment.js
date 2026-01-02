define([
    'Magento_Checkout/js/model/quote',
    'jquery'
], function (quote, $) {
    'use strict';

    return function () {

        // FORCE PAYMENT SHOW
        quote.shippingMethod.subscribe(function () {
            $('.payment-methods').show();
        });

        $(document).ready(function () {
            $('.payment-methods').show();
        });
    };
});
