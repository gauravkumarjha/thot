<?php

namespace DiMedia\NotifyForm\Model;

use Magento\Framework\Model\AbstractModel;

class Form extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('DiMedia\NotifyForm\Model\ResourceModel\Form');
    }
}
