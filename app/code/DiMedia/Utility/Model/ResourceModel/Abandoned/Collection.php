<?php
declare(strict_types=1);

namespace DiMedia\Utility\Model\ResourceModel\Abandoned;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DiMedia\Utility\Model\Abandoned;
use DiMedia\Utility\Model\ResourceModel\Abandoned as AbandonedResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Abandoned::class, AbandonedResource::class);
    }
}
