/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_PhonePe
 * @author     Webkul Software Private Limited
 * @copyright  Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'mage/translate',
        'mage/url',
        'Webkul_PhonePe/js/action/set-payment-method',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function (
        ko,
        $,
        Component,
        selectPaymentMethodAction,
        checkoutData,
        quote,
        totals,
        $t,
        url,
        setPaymentMethodAction,
        customerData,
        additionalValidators
    ) {
        'use strict';
        var phonepeConfig = window.checkoutConfig.payment.phonepe;
        return Component.extend(
            {
                defaults: {
                    template: 'Webkul_PhonePe/payment/phonepe',
                    code: phonepeConfig.code,
                    methodtitle :phonepeConfig.title
                },

              /**
               * @override
               */
                initObservable: function () {
                    this._super()
                    .observe([
                        'methodtitle'
                    ]);
                    return this;
                },
                getMethodTitle:function() {
                    return 
                },
                /**
                 * Redirect to phonepe
                 */
                generateOrder: function () {
                    if (additionalValidators.validate()) {
                        var indexUrl = url.build('phonepe/phonepe/index');
                        //update payment method information if additional data was changed
                        this.selectPaymentMethod();
                        customerData.invalidate(['cart']);
                        setPaymentMethodAction(this.messageContainer).done(
                            function () {
                                $.mage.redirect(
                                    indexUrl
                                );
                            }
                        );

                        return false;
                    }
                },

                /**
                 * selectPaymentMethod called when payment method is selected
                 * 
                 * @return boolean
                 */
                selectPaymentMethod: function () {
                    selectPaymentMethodAction(this.getData());
                    checkoutData.setSelectedPaymentMethod('phonepe');                    
                    return true;                 
                },

                /**
                 * getData set payment method data for making it available in PaymentMethod Class
                 *
                 * @return object
                 */
                getData: function () {
                    var self = this;
                    return {
                        'method': 'phonepe',
                        'additional_data': {},
                    };
                }
            }
        );
    }
);
