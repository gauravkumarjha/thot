<?php

namespace V4U\ComingSoonNotify\Model\ResourceModel\Contact;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use V4U\ComingSoonNotify\Model\Contact as Model;
use V4U\ComingSoonNotify\Model\ResourceModel\Contact as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
