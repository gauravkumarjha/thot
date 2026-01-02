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
var config = {
    map: {
        '*': {
            'tracking': 'Bss_GA4/js/tracking'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-payment-method': {
                'Bss_GA4/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-method': {
                'Bss_GA4/js/action/select-shipping-method-mixin': true
            }
        }
    }
};
