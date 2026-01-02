<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Grid\Renderer;

use Magento\Sales\Model\Order\Item;

class Cgstcharge extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $orderObj;

    public function __construct(
        Item $order
    ) {
        $this->orderObj = $order;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $value = parent::render($row);

        if ($value > 0) {

            $order = $this->orderObj->load($value);
            return $order->getCgstCharge();

        }
        return '';
    }
}
