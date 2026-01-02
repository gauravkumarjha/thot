<?php

declare(strict_types=1);

namespace Chetaru\Edit\Service;

use Chetaru\Edit\Model\ResourceModel\Post\Collection;
use Chetaru\Edit\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\DB\Select;

class PostsProvider
{
    public function __construct(private CollectionFactory $collectionFactory)
    {}

    public function getPosts(int $limit, int $currentPage): Collection
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('status', ['eq' => '1']);
        $collection->setOrder('creation_time', Select::SQL_DESC);
        $collection->setPageSize($limit);
        $collection->setCurPage($currentPage);

        return $collection;
    }

    private function getCollection(): Collection
    {
        return $this->collectionFactory->create();
    }
}
