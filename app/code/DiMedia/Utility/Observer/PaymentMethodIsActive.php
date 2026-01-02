<?php

namespace DiMedia\Utility\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;

class PaymentMethodIsActive implements ObserverInterface
{
    protected $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function execute(Observer $observer)
    {
        $method = $observer->getEvent()->getMethodInstance();
        $result = $observer->getEvent()->getResult();
        $quote = $observer->getEvent()->getQuote();

        if (!$quote instanceof Quote) {
            return;
        }

        $amount = $quote->getGrandTotal();
        $shippingCountry = $quote->getShippingAddress()->getCountryId();

        $phonepeCode = 'phonepe';      // replace with actual code of PhonePe
        $razorpayCode = 'razorpay';    // replace with actual code of Razorpay

        if (($method->getCode() === $phonepeCode && $amount > 200000) || $shippingCountry !== 'IN') {
            $result->setData('is_available', false);
        }
        if($shippingCountry !== 'IN' && $method->getCode() === $razorpayCode) {
            $result->setData('is_available', true);
        } else if ($method->getCode() === $razorpayCode && $amount <= 200000 && $shippingCountry === 'IN') {
            $result->setData('is_available', false);
        } 
    }
}
