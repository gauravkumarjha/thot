<?php

namespace V4U\BrochureForm\Model;

use Magento\Framework\Model\AbstractModel;

class Form extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\V4U\BrochureForm\Model\ResourceModel\Form::class);
    }
}
