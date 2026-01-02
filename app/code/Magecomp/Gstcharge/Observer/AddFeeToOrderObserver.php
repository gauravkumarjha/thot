<?php
namespace Magecomp\Gstcharge\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $quote = $observer->getQuote();
        $CgstfeeFee = $quote->getShippingAddress()->getCgstCharge();
        $SgstfeeFee = $quote->getShippingAddress()->getSgstCharge();
        $IgstfeeFee = $quote->getShippingAddress()->getIgstCharge();
       
        //Set fee data to order
        $order = $observer->getOrder();
        
        $order->setData('shipping_sgst_charge', $quote->getShippingAddress()->getShippingSgstCharge());
        $order->setData('shipping_cgst_charge', $quote->getShippingAddress()->getShippingCgstCharge());
        $order->setData('shipping_igst_charge', $quote->getShippingAddress()->getShippingIgstCharge());
        
        $order->setData('cgst_charge', $CgstfeeFee);
        $order->setData('sgst_charge', $SgstfeeFee);
        $order->setData('igst_charge', $IgstfeeFee);
        
        return $this;
    }
}
