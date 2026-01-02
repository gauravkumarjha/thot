define([
    'Magento_Checkout/js/view/checkout',
    'ko',
    'uiRegistry'
], function (CheckoutView, ko, registry) {
    'use strict';

    return CheckoutView.extend({
        defaults: {
            template: 'DiMedia_SingleCheckout/single-checkout'
        },

        isAddressValid: ko.observable(false),

        initialize() {
            this._super();
            console.log("🔥 Unified Checkout Loaded — JS is now working");

            var self = this;

            registry.async('checkoutProvider')(function (provider) {

                provider.on('shippingAddress', function (addr) {
                    self.isAddressValid(self.validate(addr));
                });

                let initial = provider.get('shippingAddress') || {};
                self.isAddressValid(self.validate(initial));
            });

            return this;
        },

        validate(address) {
            if (!address) return false;

            let fields = ['firstname', 'lastname', 'street', 'city', 'postcode', 'country_id', 'telephone'];

            for (let f of fields) {
                let v = address[f];

                if (f === 'street') {
                    if (!v || !v[0]) return false;
                    continue;
                }

                if (!v) return false;
            }

            return true;
        }
    });
});
