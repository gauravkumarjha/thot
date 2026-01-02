<?php

namespace DiMedia\Utility\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class RestrictStatusTransitions implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $currentStatus = $order->getStatus();
        $allowedTransitions = [
            'pending' => ['processing', 'canceled'],
            'processing' => ['shipped', 'partially_shipped'],
            'shipped' => ['delivered', 'partially_delivered', 'returned'],
            'delivered' => ['returned', 'partially_returned'],
            'returned' => ['replaced', 'refunded'],
        ];

        $newStatus = $order->getStatus();
        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Status transition from %1 to %2 is not allowed.', $currentStatus, $newStatus)
            );
        }
    }
}
