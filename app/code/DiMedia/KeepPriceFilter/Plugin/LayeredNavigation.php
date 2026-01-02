<?php

namespace DiMedia\KeepPriceFilter\Plugin;

use Magento\LayeredNavigation\Block\Navigation;

class LayeredNavigation
{
    public function afterGetFilters(Navigation $subject, $result)
    {
        // Ensure the price filter is always included
        $priceFilter = array_filter($result, function ($filter) {
            return $filter->getRequestVar() === 'price';
        });

        if (empty($priceFilter)) {
            $priceFilter = $this->createPriceFilter($subject);
            if ($priceFilter) {
                $result[] = $priceFilter;
            }
        }

        return $result;
    }

    private function createPriceFilter(Navigation $subject)
    {
        // Logic to create and return a price filter instance
        // This may involve interacting with the filter list and applying necessary parameters
        // Placeholder for actual implementation
        return null;
    }
}
