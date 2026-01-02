define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals',
    'mage/url',
    'Magento_Checkout/js/model/payment-method-list',
    'Magento_Checkout/js/action/get-payment-information'
], function ($, quote, totals, url, paymentMethodList, getPaymentInformation) {
    'use strict';

    return function () {
        // Listen for payment method selection
        $(document).on('change', 'input[name="payment[method]"]', function () {
            var selectedPaymentMethod = $(this).val();
            console.log(selectedPaymentMethod+"-Gaurav-8-10-2024");
            if (selectedPaymentMethod === 'cashondelivery') {
                // COD selected, trigger the fee addition via AJAX
                $.ajax({
                    url: 'dimedia_customstatus/ajax/addCodFee',
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Update totals on the checkout page
                            getPaymentInformation().done(function () {
                                totals.isLoading(true);
                                quote.setTotals(response.totals);
                                totals.isLoading(false);
                            });
                        }
                    }
                });
            }
        });
    };
});
