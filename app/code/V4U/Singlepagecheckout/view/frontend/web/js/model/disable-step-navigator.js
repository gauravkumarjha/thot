define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    return function (StepNavigator) {

        // Remove all default steps
        StepNavigator.steps = ko.observableArray([]);

        // Override registerStep to block adding new steps
        StepNavigator.registerStep = function (code, alias, title, isVisible, navigate, sortOrder) {
            // Do nothing and return true to avoid errors
            return true;
        };

        // Optional: Disable next/prev step functions
        StepNavigator.next = function () { return true; };
        StepNavigator.back = function () { return true; };

        return StepNavigator;
    };
});
