<?php

namespace V4U\Singlepagecheckout\Plugin\Checkout;

class LayoutProcessor
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {

        // Remove separate billing step
        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        );

        // Billing ko shipping ke andar attach karo
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['billing-address'] = [
            'component' => 'Magento_Checkout/js/view/billing-address',
            'displayArea' => 'billing-address',
            'provider' => 'checkoutProvider',
            'deps' => [
                'checkoutProvider'
            ],
            'dataScopePrefix' => 'billingAddress',
            'sortOrder' => 500,
            'children' => [
                'billing-address-fieldset' => [
                    'component' => 'Magento_Checkout/js/view/form/components/fieldset',
                    'config' => [
                        'template' => 'Magento_Checkout/form/fieldset'
                    ],
                    'children' => []
                ]
            ]
        ];

        return $jsLayout;
    }
}
