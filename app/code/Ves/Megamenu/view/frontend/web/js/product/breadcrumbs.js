/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 define([
    'jquery',
    "Magento_Catalog/js/product/breadcrumbs"
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.breadcrumbs', widget, {
            _getParentMenuItem: function (menuItem) {
                var classes,
                    classNav,
                    parentClass,
                    parentMenuItem = null;

                if (!menuItem) {
                    return null;
                }
                if (menuItem.parent('[data-dynamic-id]').length) {
                    let pr_dnm = menuItem.parent('[data-dynamic-id]');
                    parentMenuItem = pr_dnm.closest('.nav-item.subdynamic').children('a')
                    return parentMenuItem;
                }
                classes = menuItem.parent().attr('class');
                classNav = classes.match(/(nav\-)[0-9]+(\-[0-9]+)+/gi);


                if (classNav) {
                    classNav = classNav[0];
                    parentClass = classNav.substr(0, classNav.lastIndexOf('-'));
                    let pr = menuItem.closest('.dynamic-item.'+ parentClass);
                    if  (pr.length) {
                        let id = pr.attr('id');
                        parentMenuItem = $(this.options.menuContainer).find('[data-dynamic-id=' +id + '] > a');
                    }
                    else if (parentClass.lastIndexOf('-') !== -1) {
                        parentMenuItem = $(this.options.menuContainer).find('.' + parentClass + ' > a');
                        parentMenuItem = parentMenuItem.length ? parentMenuItem : null;
                    }
                }

                return parentMenuItem;
            }
        });

        return $.mage.breadcrumbs;
    };
});
