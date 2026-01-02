/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    $(document).on('ajaxComplete', function (event, xhr, settings) {
        var response = xhr.responseJSON;
        /* Compatible with Bss_LayerNavigation */
        if (response && response.ga4_gtag !== undefined && response.ga4_gtag !== "") {
            let ga4_gtags = response.ga4_gtag;
            for (let i = 0; i < ga4_gtags.length; i++) {
                if (ga4_gtags[i] !== null && !(ga4_gtags[i] === "") && ga4_gtags !== null ) {
                    let dataViewListItem = $.parseJSON(ga4_gtags[i]);
                    let script = '<script> gtag('+ JSON.stringify(dataViewListItem[0]) + ','
                        + JSON.stringify(dataViewListItem[1]) + ',' + JSON.stringify(dataViewListItem[2]) +'); </script>';
                    $('.bss-script').append(script);
                }
            }
        }
        /* End compatible with Bss_LayerNavigation */
        if (response && response.cart !== undefined) {
            if (response.cart.bss_ga4 !== undefined && response.cart.bss_ga4 !== "") {
                var data = $.parseJSON(response.cart.bss_ga4);
                let script = '<script class="bss-add-to-cart"> gtag('+ JSON.stringify(data[0]) + ','
                    + JSON.stringify(data[1]) + ',' + JSON.stringify(data[2]) +'); </script>';
                let scriptAddToCart = $('.bss-add-to-cart');
                if (scriptAddToCart.length > 0 ) {
                    scriptAddToCart.remove();
                }
                $('.bss-script').append(script);
            }
            /*Add event add to wish list from cart*/
            if (response.cart.bss_ga4_wishlist_from_cart !== undefined && response.cart.bss_ga4_wishlist_from_cart !== "") {
                var dataAddWishList = $.parseJSON(response.cart.bss_ga4_wishlist_from_cart);
                let script = '<script> gtag('+ JSON.stringify(dataAddWishList[0]) + ','
                    + JSON.stringify(dataAddWishList[1]) + ',' + JSON.stringify(dataAddWishList[2]) +'); </script>';
                $('.bss-script').append(script);
            }
        }
    });

    var miniCart = $('[data-block=\'minicart\']');
    miniCart.on('dropdowndialogopen', function () {
        let cart = customerData.get('cart')();
        if (cart.items.length > 0) {
            let items = [];
            $.each(cart.items, function ( index, item ) {
                let item_variant = '';
                if (item.product_type === "configurable" && item.options != null) {
                    $.each(item.options, function (key, option) {
                        let variantOption = option.label + ': ' + option.value;
                        item_variant = item_variant + variantOption + ',';
                    });
                    item_variant = item_variant.substring(0, item_variant.length - 1);
                }
                let data = {
                    item_id: item.product_sku,
                    item_name: item.product_name,
                    index: parseInt(index) + 1,
                    item_variant: item_variant,
                    price: item.product_price_value,
                    quantity: item.qty
                };
                items.push(data);
            });
            let params = {
                currency: cart.currency,
                value: cart.subtotalAmount,
                items: items
            };
            let script = '<script class="bss-view-mini-cart"> gtag("event","view_cart",' + JSON.stringify(params) +'); </script>';
            let viewCart = $('.bss_view_cart');
            if (viewCart.length <= 0 ) {
                $('.bss-script').append(script);
            }
        }
    });
});
