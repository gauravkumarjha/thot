<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Grid\Renderer;

use Magento\Sales\Model\Order\Item;

class Gstrate extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $proditem;

    public function __construct(
        Item $proditem
    ) {
        $this->proditem = $proditem;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $value = parent::render($row);

        if ($value > 0) {
            $item = $this->proditem->load($value);
            return $item->getCgstPercent() + $item->getSgstPercent() + $item->getIgstPercent();
        }
        return '';
    }
}
