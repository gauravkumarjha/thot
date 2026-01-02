<?php
namespace DiMedia\ShippingDayUnicomerce\Block\Adminhtml\Order;


use Magento\Backend\Block\Template;
use Magento\Sales\Model\OrderFactory;

class FulfillmentDate extends Template
{
    protected $orderFactory;

    public function __construct(
        Template\Context $context,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderFactory = $orderFactory;
    }

    public function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->orderFactory->create()->load($orderId);
    }

    public function getFulfillmentDate()
    {
        $order = $this->getOrder();
        return $order->getData('fulfillment_date'); // Ensure 'fulfillment_date' is saved in the order
    }
}
