define(['ko'], function (ko) {
    'use strict';

    return function (Component) { 
        return Component.extend({
            initialize: function () {
                this._super();

                // Check window width (optional: only open on desktop)
                if (window.innerWidth >= 768) {
                    this.isItemsBlockExpanded(true); // Set value directly
                }

                // Debug to ensure it's applied
                console.log('✅ Sidebar Mixin Applied');

                return this;
            },
            toggleItemsBlock: function () {
                // Force block to remain open
                this.isItemsBlockExpanded(true);
            }
        });
    };
});
