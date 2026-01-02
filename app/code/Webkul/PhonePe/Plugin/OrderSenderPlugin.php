<?php

namespace Webkul\PhonePe\Plugin;

use Psr\Log\LoggerInterface;

class OrderSenderPlugin
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Around plugin for OrderSender::send
     *
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject
     * @param callable $proceed
     * @param \Magento\Sales\Model\Order $order
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSend($subject, callable $proceed, $order, $forceSyncMode = false)
    {
        try {
            $payment = $order->getPayment();
            $method = $payment ? $payment->getMethod() : null;
            $state = $order->getState();
            // $this->logger->info('Order Basic Info: ' . json_encode([
            //     'order_id'      => $order->getId(),
            //     'increment_id'  => $order->getIncrementId(),
            //     'state'         => $order->getState(),
            //     'status'        => $order->getStatus(),
            //     'grand_total'   => $order->getGrandTotal(),
            //     'customer_email' => $order->getCustomerEmail(),
            // ], JSON_PRETTY_PRINT));

            // $this->logger->info('Payment Basic Info: ' . json_encode([
            //     'method'        => $payment->getMethod(),
            //     'amount_ordered' => $payment->getAmountOrdered(),
            //     'additional_information' => $payment->getAdditionalInformation(),
            // ], JSON_PRETTY_PRINT));



            $this->logger->info('PhonePe pending order — trigger.'. $method."-". $state."-". \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT."-". \Magento\Sales\Model\Order::STATE_NEW.'. Order: ' . $order->getIncrementId());
            // adjust 'phonepe' to your real payment method code if different
            if ($method === 'phonepe' && in_array($state, [\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, \Magento\Sales\Model\Order::STATE_NEW])) {
               // $this->logger->info('PhonePe pending order — skipping order email. Order: ' . $order->getIncrementId());
                $this->logger->info('🛑 Skipping order email for PhonePe (state: ' . $state . ') - Order: ' . $order->getIncrementId());
                return false; // block sending email
            }
        } catch (\Exception $e) {
            // अगर कोई गलती हो तो proceed कर दें ताकि बुरी तरह से सिस्टम ब्रोक न हो
            $this->logger->error('OrderSenderPlugin exception: ' . $e->getMessage());
        }
        // अन्यथा सामान्य flow
        return $proceed($order, $forceSyncMode);
    }
}
