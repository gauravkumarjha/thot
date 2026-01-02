define([
    'Magento_Checkout/js/checkout-state'
], function (state) {
    'use strict';

    return function (Component) {
        return Component.extend({

            isShippingOpen: state.isShippingOpen,

            toggleShipping: function () {
                // Shipping toggle allowed
                state.isShippingOpen(!state.isShippingOpen());

                // Rule: dono band nahi hone chahiye
                if (!state.isShippingOpen()) {
                    state.isPaymentOpen(true);
                }
            },

            setShippingInformation: function () {

                // 🔴 STEP 1: run original validation
                this._super();

                // 🔴 STEP 2: check validation result
                if (this.source.get('params.invalid')) {
                    // ❌ validation fail → payment open mat karo
                    state.isPaymentEnabled(false);
                    return;
                }

                // ✅ STEP 3: validation success
                state.isShippingValid(true); 
                state.isPaymentEnabled(true);
                state.isShippingOpen(false);
                state.isPaymentOpen(true);
            }
        });
    };
});
