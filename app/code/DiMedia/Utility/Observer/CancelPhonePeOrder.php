<?php

namespace DiMedia\Utility\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;

class CancelPhonePeOrder implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $paymentMethod = $order->getPayment()->getMethod();

        if ($paymentMethod == 'phonepe') { // Replace with your actual method code
            $order->setState(Order::STATE_CANCELED)
                  ->setStatus(Order::STATE_CANCELED);
            $order->addStatusHistoryComment(__('PhonePe payment pending — temporarily canceled.'));
            $order->save();
        }
    }
}
