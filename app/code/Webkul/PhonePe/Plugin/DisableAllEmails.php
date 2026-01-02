<?php

namespace Webkul\PhonePe\Plugin;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class DisableAllEmails
{
    protected $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function aroundSend($subject, \Closure $proceed, $entity, $forceSyncMode = false)
    {
        try {
            $order = ($entity instanceof Order) ? $entity : $entity->getOrder();
            $method = $order->getPayment()->getMethod();
            $state  = $order->getState();

            if (
                strpos($method, 'phonepe') !== false &&
                in_array($state, [Order::STATE_PENDING_PAYMENT, Order::STATE_CANCELED])
            ) {
                $this->logger->info('❌ Blocked email for PhonePe Order #' . $order->getIncrementId() . ' | State: ' . $state);
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->info('Email plugin exception: ' . $e->getMessage());
        }

        return $proceed($entity, $forceSyncMode);
    }
}
