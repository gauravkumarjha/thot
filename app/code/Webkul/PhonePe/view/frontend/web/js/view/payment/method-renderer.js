/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_PhonePe
 * @author     Webkul Software Private Limited
 * @copyright  Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        /**
         * push phonepe renderer in the default renderer list
         */
        rendererList.push(
            {
                type: 'phonepe',
                component: 'Webkul_PhonePe/js/view/payment/method-renderer/phonepe'
            }
        );

        return Component.extend({});
    }
);