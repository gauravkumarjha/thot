<?php

namespace DiMedia\BestsellerSort\Plugin\Catalog\Model;

class Config
{
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $subject,
        $result
    ) {
        $result['bestseller'] = __('Best Seller');
      
        return $result;
    }
}
