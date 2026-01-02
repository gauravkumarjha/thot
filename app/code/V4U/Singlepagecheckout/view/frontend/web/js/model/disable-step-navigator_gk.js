define([], function () {
    'use strict';

    return function (stepNavigator) {

        const originalRegister = stepNavigator.registerStep;

        stepNavigator.registerStep = function (
            code, alias, title, isVisible, navigate, sortOrder
        ) {
            isVisible(true);
            return originalRegister.call(
                this,
                code,
                alias,
                title,
                isVisible,
                navigate,
                sortOrder
            );
        };

        stepNavigator.navigateTo = function () {
            return true;
        };

        return stepNavigator;
    };
});
