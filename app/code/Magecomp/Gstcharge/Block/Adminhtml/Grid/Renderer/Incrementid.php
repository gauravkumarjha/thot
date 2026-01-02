<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Grid\Renderer;

class Incrementid extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $value = parent::render($row);
        if ($value > 0) {
            return '#' . $value;
        }
        return $value;
    }
}
