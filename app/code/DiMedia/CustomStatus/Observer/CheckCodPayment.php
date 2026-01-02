<?php

namespace DiMedia\CustomStatus\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class CheckCodPayment implements ObserverInterface
{

    protected $logger;
    const COD_FEE_AMOUNT = 1000; 

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger; // Initialize the logger
    }

    public function execute(Observer $observer)
    {
        // Get the order object
        $order = $observer->getEvent()->getOrder();

        // Check if the payment method is COD
        if ($order->getPayment()->getMethod() === 'cashondelivery') {
            // Get the order amount
            $currentTotal = $order->getGrandTotal();
            $newTotal = $currentTotal + self::COD_FEE_AMOUNT;
            $order->setGrandTotal($newTotal);
            $order->setBaseGrandTotal($newTotal); // Also update the base total if necessary

            // Optional: Add a comment to the order
            $order->addStatusHistoryComment('₹1000 added for Cash on Delivery fee.');
            // Do something with the amount
            // For example, log it or process it further
            $this->logger->info('COD Amount: ' . $currentTotal);
        }
    }
}
