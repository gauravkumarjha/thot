<?php

namespace DiMedia\SingleCheckout\Plugin\Checkout;

class LayoutProcessorPlugin
{
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $jsLayout
    ) {

        // Your custom component
        $jsLayout['components']['checkout']['children']['singleCheckout'] = [
            'component' => 'DiMedia_SingleCheckout/js/view/single-checkout',
            'template' => 'DiMedia_SingleCheckout/single-checkout',
            'displayArea' => 'content',
            'sortOrder' => 5
        ];

        // Only add structure if exists → NO BREAK
        $paymentPath = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children'];

        if (isset($paymentPath['renders']['children'])) {

            foreach ($paymentPath['renders']['children'] as $groupName => &$group) {
                if (!isset($group['methods'])) {
                    $group['methods'] = [];
                }
            }
        }

        return $jsLayout;
    }
}
