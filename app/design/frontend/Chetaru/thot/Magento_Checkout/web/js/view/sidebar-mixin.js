define(['ko', 'jquery'], function (ko, $) {
    'use strict';

    return function (Component) {
        return Component.extend({
            initialize: function () {
                this._super();

                console.log('✅ Sidebar Mixin Applied');

                // Delay to ensure DOM is ready
                setTimeout(() => {
                    if (window.innerWidth >= 768) {
                        const titleElements = document.querySelectorAll('.items-in-cart .title');

                        if (titleElements.length > 0) {
                            titleElements[0].click(); // Trigger click on first element
                            console.log('✅ Sidebar Mixin Click Triggered');
                        } else {
                            console.warn('⚠️ No .title element found inside .items-in-cart');
                        }
                    }
                }, 3000); // Delay ensures elements are loaded

                return this;
            }
        });
    };
});
