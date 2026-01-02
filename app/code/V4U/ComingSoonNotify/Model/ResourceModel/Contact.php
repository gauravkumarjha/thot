<?php

namespace V4U\ComingSoonNotify\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Contact extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('v4u_comingsoon_notify', 'id');
    }
}
