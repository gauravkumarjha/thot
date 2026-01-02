<?php

namespace Webkul\PhonePe\Plugin;

use Magento\Framework\Mail\TransportInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class BlockTransportEmail
{
    protected $orderRepository;
    protected $logger;

    public function __construct(OrderRepositoryInterface $orderRepository, LoggerInterface $logger)
    {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    public function aroundSendMessage(TransportInterface $subject, \Closure $proceed)
    {
        try {
            $message = $subject->getMessage();

            // Get raw email body safely
            $bodyContent = '';
            $body = $message->getBody();

            if ($body instanceof \Laminas\Mime\Message) {
                foreach ($body->getParts() as $part) {
                    $bodyContent .= $part->getContent();
                }
            } else {
                // Fallback for plain text email
                $bodyContent = (string) $body;
            }

            // Try to extract order increment ID
            if (preg_match('/Order\s+#(\d+)/', $bodyContent, $matches)) {
                $incrementId = $matches[1];
                $order = $this->orderRepository->get($incrementId);
                $method = $order->getPayment()->getMethod();
                $state = $order->getState();

                if (
                    strpos($method, 'phonepe') !== false &&
                    in_array($state, [
                        \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,
                    ])
                ) {
                    $this->logger->info('❌ Blocked Email at Transport | Order #' . $incrementId . ' | State: ' . $state);
                    return $subject; // Skip sending
                }
            }
        } catch (\Exception $e) {
            $this->logger->info('Email Blocker Exception: ' . $e->getMessage());
        }

        return $proceed();
    }
}
