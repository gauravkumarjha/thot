define([
    'Magento_Checkout/js/checkout-state'
], function (state) {
    'use strict';

    return function (Component) {
        return Component.extend({

            isPaymentOpen: state.isPaymentOpen,
            isPaymentEnabled: state.isPaymentEnabled,

            togglePayment: function () {
                // First time disabled
                if (!state.isPaymentEnabled()) {
                    return;
                }

                state.isPaymentOpen(!state.isPaymentOpen());

                // Rule: dono band nahi
                if (!state.isPaymentOpen()) {
                    state.isShippingOpen(true);
                }
            }
        });
    };
});
