<?php

namespace Mageplaza\Simpleshipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SaveCustomAttributeToOrderItem implements ObserverInterface
{
    protected $orderItemRepository;

    public function __construct(
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
    ) {
        $this->orderItemRepository = $orderItemRepository;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $billingAddress = $order->getBillingAddress();
        $countryId  = $billingAddress->getCountryId();

      
    }
}
