<?php

namespace Webkul\PhonePe\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Psr\Log\LoggerInterface;

class SendEmailAfterPayment implements ObserverInterface
{
    protected $orderSender;
    protected $logger;

    public function __construct(OrderSender $orderSender, LoggerInterface $logger)
    {
        $this->orderSender = $orderSender;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $payment = $observer->getEvent()->getPayment();
        $order = $payment->getOrder();
        $method = $payment->getMethod();
        $additional = $payment->getAdditionalInformation();

        // Only check for PhonePe
        if ($method === 'phonepe' && isset($additional['raw_details_info'])) {
            $info = $additional['raw_details_info'];
            if (is_string($info)) {
                $info = json_decode($info, true);
            }

            if (!empty($info['merchantTransId']) && !$order->getEmailSent()) {
                try {
                    $this->orderSender->send($order);
                    $this->logger->info('✅ Observer: Sent order email after PhonePe success for Order: ' . $order->getIncrementId());
                } catch (\Exception $e) {
                    $this->logger->error('❌ Observer: Failed to send email: ' . $e->getMessage());
                }
            }
        }
    }
}
