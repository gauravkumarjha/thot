<?php

namespace DiMedia\NotifyForm\Model\ResourceModel\Form;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DiMedia\NotifyForm\Model\Form as Model;
use DiMedia\NotifyForm\Model\ResourceModel\Form as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
