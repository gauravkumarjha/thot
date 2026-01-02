<?php

namespace V4U\ComingSoonNotify\Model;

use Magento\Framework\Model\AbstractModel;

class Contact extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\V4U\ComingSoonNotify\Model\ResourceModel\Contact::class);
    }
}
