<?php

namespace Vendor\CustomAttributes\Plugin;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class OrderItemsDataProviderPlugin
{
    public function afterGetData(DataProviderInterface $subject, $result)
    {
        foreach ($result['items'] as &$item) {
            if (isset($item['item_id'])) {
                // Assuming 'custom_attribute' is the key for your custom attribute in the order item table
                $item['custom_shipping_price'] = $item['extension_attributes']['custom_shipping_price'] ?? null;
            }
        }
        return $result;
    }
}
