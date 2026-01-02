define(['ko'], function (ko) {
    'use strict';

    return {
        isShippingOpen: ko.observable(true),
        isPaymentOpen: ko.observable(false),

        isPaymentEnabled: ko.observable(false),
        isShippingValid: ko.observable(false)
    };
});
