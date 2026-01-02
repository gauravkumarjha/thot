<?php

namespace DiMedia\ShippingDayUnicomerce\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class AddStoreCodeToOrderApi
{
    protected $extensionFactory;

    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes() ?: $this->extensionFactory->create();

        // Set store_code if available
        $storeCode = $order->getData('store_code');
        if ($storeCode) {
            $extensionAttributes->setStoreCode($storeCode);
        }

        // Set fulfillment_date, ensuring a proper datetime format
        $fulfillmentDate = $order->getData('fulfillment_date');
        if ($fulfillmentDate) {
            try {
                $formattedDate = (new \DateTime($fulfillmentDate))->format('Y-m-d');
                $extensionAttributes->setFulfillmentDate($formattedDate);
            } catch (\Exception $e) {
                // Handle invalid date format gracefully
                $extensionAttributes->setFulfillmentDate("0000-00-00");
            }
        } else {
            $extensionAttributes->setFulfillmentDate("0000-00-00");
        }

        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }

    public function afterGetList(OrderRepositoryInterface $subject, $orderSearchResult)
    {
        foreach ($orderSearchResult->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $orderSearchResult;
    }
}
