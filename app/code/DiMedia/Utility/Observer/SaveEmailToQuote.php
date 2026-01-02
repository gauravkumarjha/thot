<?php

namespace DiMedia\Utility\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

class SaveGuestEmailToQuote implements ObserverInterface
{
    protected $quoteRepository;
    protected $logger;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        // Logged-in user? Skip
        // if ($quote->getCustomerId()) {
        //     return;
        // }

        $shippingAddress = $quote->getShippingAddress();
        $billingAddress  = $quote->getBillingAddress();
        $email = null;

        // 1) Try from Shipping Address first
        if ($shippingAddress && $shippingAddress->getEmail()) {
            $email = $shippingAddress->getEmail();
        }
        // 2) Or from Billing Address
        elseif ($billingAddress && $billingAddress->getEmail()) {
            $email = $billingAddress->getEmail();
        }

        if ($email && !$quote->getCustomerEmail()) {
            $quote->setCustomerEmail($email);
            try {
                $this->quoteRepository->save($quote);
                $this->logger->info("Guest Email Saved: " . $email . " | Quote ID: " . $quote->getId(), ['file' => 'guest_email.log']);
            } catch (\Exception $e) {
                $this->logger->error("Error Saving Guest Email: " . $e->getMessage(), ['file' => 'guest_email.log']);
            }
        } else {
            $this->logger->info("Email not found or already exists. Quote ID: " . $quote->getId(), ['file' => 'guest_email.log']);
        }
    }
}
