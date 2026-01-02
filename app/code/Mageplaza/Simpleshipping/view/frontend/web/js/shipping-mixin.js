define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Customer/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals'
], function ($, wrapper, addressConverter, quote, totals) {
    'use strict';

    return function (target) {
        return wrapper.extend(target, {
            saveShippingAddress: function () {
                this._super();

                // Force update of currency symbol
                totals.isLoading.subscribe(function (isLoading) {
                    if (!isLoading) {
                        var currencySymbol = window.checkoutConfig.quoteData.quote_currency_symbol;

                        $('.price').each(function () {
                            var priceText = $(this).text();

                            // Replace existing currency symbols with the current one
                            var updatedPrice = priceText.replace(/[\$€£₹AED]/g, currencySymbol);
                            $(this).text(updatedPrice);

                            console.log("Currency updated: ", currencySymbol, "Original Price: ", priceText, "Updated Price: ", updatedPrice);
                        });
                    }
                });
            }
        });
    };
});
