define([], function () {
    'use strict';

    return function (Component) {
        return Component.extend({
            isFullMode: function () {
                if (!this.getTotals()) {
                    return false;
                }

                return true;
            }
        });
    };
});