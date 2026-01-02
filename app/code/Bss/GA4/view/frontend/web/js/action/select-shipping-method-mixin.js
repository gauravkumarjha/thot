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
 * @copyright  Copyright (c) 2022-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'mage/url'
], function ($, wrapper, quote, urlBuilder) {
    'use strict';

    return function (selectShippingMethodAction) {
        return wrapper.wrap(selectShippingMethodAction, function (originalSelectShippingMethodAction, shippingMethod) {

            originalSelectShippingMethodAction(shippingMethod);

            if (shippingMethod === null) {
                return;
            }
            $.ajax({
                url: urlBuilder.build('ga4/select/shipping'),
                data: {
                    method: shippingMethod['carrier_title']
                },
                type: 'post',
                dataType: "json",
                cache: false,
                success: function (res) {
                    let element = $('.bss_add_shipping_info');
                    if (element.length > 0 ) {
                        element.remove();
                    }
                    if (typeof(res.output) != "undefined"){
                        $('#checkout-step-shipping_method').append(res.output);
                    }
                },
                error: function (res) {
                    console.log('send data event add shipping info fail');
                }
            });
        });
    };

});
