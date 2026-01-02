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

    return function (selectPaymentMethodAction) {
        return wrapper.wrap(selectPaymentMethodAction, function (originalSelectPaymentMethodAction, paymentMethod) {

            originalSelectPaymentMethodAction(paymentMethod);

            if (paymentMethod === null) {
                return;
            }
            $.ajax({
                url: urlBuilder.build('ga4/select/payment'),
                data: {
                    method: paymentMethod.method
                },
                type: 'post',
                dataType: "json",
                cache: false,
                success: function (res) {
                    let element = $('.bss_add_payment_info');
                    if (element.length > 0 ) {
                        element.remove();
                    }
                    if (typeof(res.output) != "undefined"){
                        if ($('#checkout-step-payment').length > 0) {
                            $('#checkout-step-payment').append(res.output);
                        }
                        if ($('#multishipping-billing-form').length > 0) {
                            $('#multishipping-billing-form').append(res.output);
                        }
                    }
                },
                error: function (res) {
                    console.log('send data event add payment info fail');
                }
            });
        });
    };

});
