<?php

namespace DiMedia\ShippingDayUnicomerce\Block\Adminhtml\Sales\Order\View\Items\Column;

use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;

class FulfillmentDate extends DefaultColumn
{
    public function getFulfillmentDate()
    {
        $item = $this->getItem();
        return $item->getData('fulfillment_date');
    }
}
