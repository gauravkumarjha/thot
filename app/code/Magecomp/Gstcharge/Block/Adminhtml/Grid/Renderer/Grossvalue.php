<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Grid\Renderer;

use Magento\Sales\Model\Order;

class Grossvalue extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $orderObj;

    public function __construct(
        Order $order
    ) {
        $this->orderObj = $order;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $value = parent::render($row);

        if ($value > 0) {

            $order = $this->orderObj->load($value);
            $orderItems = $order->getAllVisibleItems();
            $returnValue = 0;

            foreach ($orderItems as $item) {
                if ($item->getExclPrice()) {
                    $returnValue += $item->getRowTotal() + $item->getCgstCharge() + $item->getSgstCharge() + $item->getIgstCharge();
                } else {
                    $returnValue += $item->getRowTotal();
                }
            }
            return $returnValue + $order->getShippingAmount();
        }
        return '';
    }
}
