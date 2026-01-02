<?php

namespace DiMedia\SingleCheckout\Plugin\Checkout;

class LayoutProcessor
{
    /**
     * Modify the jsLayout to use your custom checkout component
     * and remove/rearrange default steps if necessary.
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ): array {
        // 1. Swap the main component to your custom one
        if (isset($jsLayout['components']['checkout']['children'])) {
            $jsLayout['components']['checkout']['component'] = 'DiMedia_SingleCheckout/js/view/single-checkout';

            // IMPORTANT: Ensure the children components (like sidebar) are still present.
            // You can restructure them here, but DO NOT REMOVE the core models.

            // Example: Modify the default 'steps' structure to fit your accordion logic
            // (e.g., removing the default step-navigator dependency if you use your own)

            // For the 'quoteData' error, focus on ensuring that components 
            // like 'sidebar' and its contents remain loaded, as they often
            // initialize key data structures.
        }

        return $jsLayout;
    }
}
