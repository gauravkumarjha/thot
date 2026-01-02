<?php

declare(strict_types=1);

namespace Chetaru\Article\Service;

use Chetaru\Article\Model\ResourceModel\Post\Collection;
use Chetaru\Article\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\DB\Select;

class PostsProvider
{
    public function __construct(private CollectionFactory $collectionFactory)
    {}

    public function getPosts(int $limit, int $currentPage): Collection
    {
        $collection = $this->getCollection();
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
