/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    'jquery'
], function ($) {
    'use strict';
    return function (measurement) {
        $.validator.addMethod(
            'validate-measurement-id',
            function (value) {
                let Regx = /^G-/g;
                return Regx.exec(value);
            },
            $.mage.__('Please enter the same as the form: G-xxxxxxx.')
        );
        return measurement;
    };
});
