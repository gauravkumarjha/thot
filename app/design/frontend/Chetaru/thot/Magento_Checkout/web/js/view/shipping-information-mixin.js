define([
    'Magento_Checkout/js/checkout-state'
], function (state) {
    'use strict';

    return function (Component) {
        return Component.extend({

            isShippingValid: state.isShippingValid,

            isVisible: function () {
                // default Magento visibility
                return this._super() && state.isShippingValid();
            }
        });
    };
});
