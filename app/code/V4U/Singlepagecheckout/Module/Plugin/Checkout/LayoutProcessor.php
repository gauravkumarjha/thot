<?php

namespace V4U\Singlepagecheckout\Plugin\Checkout;

class LayoutProcessor
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {

        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['billing-address']
        );

        // Billing ko shipping ke andar attach karo
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['billing-address'] = [
            'component' => 'Magento_Checkout/js/view/billing-address',
            'displayArea' => 'billing-address',
            'provider' => 'checkoutProvider',
            'deps' => [
                'checkout.steps.shipping-step.shippingAddress'
            ],
            'dataScopePrefix' => 'billingAddress',
            'sortOrder' => 250,
            'children' => [
                'form-fields' => [
                    'component' => 'Magento_Checkout/js/view/billing-address/form',
                    'displayArea' => 'form-fields'
                ]
            ]
        ];


        return $jsLayout;
    }
}
