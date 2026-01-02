<?php

namespace DiMedia\NotifyForm\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Form extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('notify_form', 'entity_id');
    }
}
