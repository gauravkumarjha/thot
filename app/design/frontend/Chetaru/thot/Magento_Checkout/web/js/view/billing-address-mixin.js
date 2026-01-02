define([
    'ko',
    'Magento_Checkout/js/model/quote'
], function (ko, quote) {
    'use strict';

    return function (BillingAddress) {
        return BillingAddress.extend({

            initialize: function () {
                this._super();

                // 🔹 ensure checkbox default checked
                if (typeof this.isAddressSameAsShipping === 'function') {
                    this.isAddressSameAsShipping(true);
                } else if (ko.isObservable(this.isAddressSameAsShipping)) {
                    this.isAddressSameAsShipping(true);
                }

                return this;
            }

        });
    };
});
