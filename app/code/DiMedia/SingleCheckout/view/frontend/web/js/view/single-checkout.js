define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/view/summary/item/details/thumbnai',
    'Magento_Paypal/js/view/payment/paypal-payments',
    'Magento_Shipping/js/view/checkout/shipping/shipping-policy',
    'Mageplaza_Simpleshipping/js/view/summary/item/details',
    'Magento_Checkout/js/view/summary/item/details/message',
    'jquery'
], function (ko, Component, stepNavigator, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'DiMedia_SingleCheckout/single-checkout',
            activeSection: 'shipping' // Initial active section
        },

        // Knockout observable to track the currently open section
        activeSection: ko.observable('shipping'),

        initialize: function () {
            this._super();
            // Optional: You might want to subscribe to payment-method-selected event
            // to potentially advance the accordion (e.g., if you only have one shipping method)
        },

        /**
         * Custom logic to convert Magento's steps/regions into a single-page accordion.
         * The standard Magento checkout handles the children components.
         * We just need to load them and control their visibility/state.
         */
        getRegion: function (name) {
            // This is how you access a child component/region defined in the jsLayout.
            return this.getChild(name);
        },

        /**
         * Toggles the active section. Closes the currently open one, opens the target one.
         * @param {string} sectionName - The name of the section to open ('shipping' or 'payment').
         */
        toggleSection: function (sectionName) {
            if (this.activeSection() === sectionName) {
                // If clicking the current section, keep it open (or close it if you prefer)
                // For this requirement, we'll just keep it open.
                return;
            }
            this.activeSection(sectionName);
        },

        /**
         * Checks if a section is active/open.
         * @param {string} sectionName - The name of the section to check.
         * @returns {boolean}
         */
        isSectionActive: function (sectionName) {
            return this.activeSection() === sectionName;
        },

        // --- Custom Validation/Advancement Logic ---

        /**
         * Advance to the payment section after shipping is complete.
         */
        moveToPayment: function () {
            // Add your validation logic here before advancing
            // e.g., check if shipping address and method are selected/valid.

            // For a simple demo:
            this.activeSection('payment');
        }
    });
});