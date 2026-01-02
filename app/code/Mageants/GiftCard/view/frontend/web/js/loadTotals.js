define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/checkout-data'
], function ($, getTotalsAction, customerData, checkoutData) {

    $(document).ready(function () {
        setTimeout(function () {
            var form = $('form#form-validate');
            var selectedShippingMethod = checkoutData.getSelectedShippingRate();
            if (selectedShippingMethod === null) {
                $.ajax({
                    url: form.attr('action'),
                    data: form.serialize(),
                    showLoader: true,
                    success: function (res) {
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });
            }
        }, 1000);
    });
});
