define(['uiRegistry'], function (registry) {
    'use strict';

    return function (List) {
        return List.extend({
            initialize: function () {
                this._super();

                var self = this;

                registry.async('singleCheckout')(function (cmp) {
                    cmp.isAddressValid.subscribe(function (valid) {
                        self.toggle(valid);
                    });

                    self.toggle(cmp.isAddressValid());
                });

                return this;
            },

            toggle: function (valid) {
                this.elems().forEach(function (method) {
                    method.isDisabledByAddress = !valid;
                });
            }
        });
    };
});
