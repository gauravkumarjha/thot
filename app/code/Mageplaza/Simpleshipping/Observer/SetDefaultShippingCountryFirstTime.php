<?php

namespace Mageplaza\Simpleshipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Session\SessionManagerInterface;

class SetDefaultShippingCountryFirstTime implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     * @param SessionManagerInterface $session
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        SessionManagerInterface $session
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->session = $session;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        // Check if the flag for the first load is already set
        if (!$this->session->getIsShippingCountrySet()) {
            $quote = $this->checkoutSession->getQuote();
            $shippingAddress = $quote->getShippingAddress();

            // Default country code
            $countryId = "IN"; // or use a dynamic value if needed

            // Set country in the shipping address
            $shippingAddress->setCountryId($countryId);

            // Save the address and quote to apply the change
            $shippingAddress->save();
            $quote->save();

            // Set the session flag to prevent it from running again
            $this->session->setIsShippingCountrySet(true);
        }
    }
}
