define([
    'uiComponent',
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-service',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/error-processor'
], function (Component, ko, $, quote, shippingService, rateService, stepNavigator, errorProcessor) {
    'use strict';

    /**
     * Accordion view model:
     * - Controls slide/toggle behavior
     * - Observes shipping address + shipping method
     * - Auto-opens Payment when shipping is valid and method selected
     * - Relies on Magento's default checkout engine for data flows
     */
    return Component.extend({
        defaults: {
            template: 'V4U_Singlepagecheckout/accordion'
        },

        initialize: function () {
            this._super();

            // Accordion state
            this.isShippingOpen = ko.observable(true);
            this.isPaymentOpen = ko.observable(false);

            // Validation: simple heuristic over shipping address fields
            this.isShippingAddressValid = ko.pureComputed(function () {
                var addr = quote.shippingAddress && quote.shippingAddress();
                if (!addr) {
                    return false;
                }
                // Basic required fields check (compatible with guest + logged in)
                var required = [
                    (addr.firstname || '').trim(),
                    (addr.lastname || '').trim(),
                    (addr.street && addr.street.length ? (addr.street.join(' ') || '').trim() : ''),
                    (addr.city || '').trim(),
                    (addr.postcode || '').trim(),
                    (addr.countryId || '').trim()
                ];
                return required.every(function (v) { return !!v; });
            });

            // When shipping address changes, try to ensure shipping methods update (core does this automatically).
            // We gently poke the rateService if available without breaking if signatures differ.
            this._subscribeAddressChangesForRates();

            // Auto-open payment when shipping is valid and shipping method is selected
            this._autoOpenPaymentOnReady();

            // Ensure initial panel heights are correct (after DOM ready)
            this._afterDomReadyApplyInitialState();

            return this;
        },

        /**
         * Subscribe to address changes to nudge shipping rates (non-disruptive).
         */
        _subscribeAddressChangesForRates: function () {
            var self = this;

            // Throttled observer to reduce noise
            var throttleMs = 400;
            var lastTimer = null;

            quote.shippingAddress.subscribe(function () {
                if (lastTimer) {
                    clearTimeout(lastTimer);
                }
                lastTimer = setTimeout(function () {
                    // Core auto-refreshes rates. If the service exposes APIs, try to call them safely.
                    try {
                        if (typeof rateService.requestRates === 'function') {
                            rateService.requestRates();
                        } else if (shippingService && typeof shippingService.setShippingRates === 'function') {
                            // No-op: let core handle rates; we avoid forcing any incompatible call.
                        }
                    } catch (e) {
                        // Do not break checkout; log via console quietly if available.
                        if (window && window.console) {
                            console.warn('[V4U] Rate refresh fallback skipped:', e);
                        }
                    }
                }, throttleMs);
            });
        },

        /**
         * Auto-open Payment when address is valid and shipping method chosen.
         */
        _autoOpenPaymentOnReady: function () {
            var self = this;

            // When shipping method is selected, open Payment
            quote.shippingMethod.subscribe(function (method) {
                if (method && self.isShippingAddressValid()) {
                    self._openPayment();
                }
            });

            // Also watch address validity; if already selected method + now valid, open Payment
            this.isShippingAddressValid.subscribe(function (valid) {
                var hasMethod = !!quote.shippingMethod();
                if (valid && hasMethod) {
                    self._openPayment();
                }
            });
        },

        /**
         * Initial state: open Shipping, collapse Payment with correct heights.
         */
        _afterDomReadyApplyInitialState: function () {
            var self = this;

            $(function () {
                // Ensure correct initial slide states
                var $ship = $('#v4u-shipping-pane');
                var $pay = $('#v4u-payment-pane');
                if (self.isShippingOpen()) {
                    self._slideOpen($ship);
                } else {
                    self._slideClose($ship);
                }
                if (self.isPaymentOpen()) {
                    self._slideOpen($pay);
                } else {
                    self._slideClose($pay);
                }
            });
        },

        /**
         * Toggle handlers for headers.
         */
        toggleShipping: function () {
            var $ship = $('#v4u-shipping-pane');
            var $pay = $('#v4u-payment-pane');

            // Always keep one open; prioritize Shipping if clicked
            this.isShippingOpen(true);
            this.isPaymentOpen(false);

            this._slideOpen($ship);
            this._slideClose($pay);
        },

        togglePayment: function () {
            // Prevent opening Payment if shipping address is invalid
            if (!this.isShippingAddressValid()) {
                // Show a non-intrusive message via Magento error processor if present
                try {
                    errorProcessor.process({ message: 'Please complete shipping information before payment.' }, null);
                } catch (e) {
                    // Fallback inline; our warning block is already visible
                }
                return;
            }

            var $ship = $('#v4u-shipping-pane');
            var $pay = $('#v4u-payment-pane');

            this.isShippingOpen(false);
            this.isPaymentOpen(true);

            this._slideClose($ship);
            this._slideOpen($pay);
        },

        /**
         * Programmatic open of Payment pane (used by auto-open logic).
         */
        _openPayment: function () {
            var $ship = $('#v4u-shipping-pane');
            var $pay = $('#v4u-payment-pane');

            this.isShippingOpen(false);
            this.isPaymentOpen(true);

            this._slideClose($ship);
            this._slideOpen($pay);
        },

        /**
         * Smooth slide helpers.
         */
        _slideOpen: function ($el) {
            if (!$el || !$el.length) return;
            if ($el.is(':visible') && $el.hasClass('open')) return;
            $el.stop(true, true).slideDown(200, function () {
                $el.addClass('open');
            });
        },

        _slideClose: function ($el) {
            if (!$el || !$el.length) return;
            if (!$el.is(':visible') && !$el.hasClass('open')) return;
            $el.stop(true, true).slideUp(200, function () {
                $el.removeClass('open');
            });
        }
    });
});
