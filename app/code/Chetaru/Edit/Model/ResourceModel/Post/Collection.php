<?php

declare(strict_types=1);

namespace Chetaru\Edit\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Chetaru\Edit\Model\Post;
use Chetaru\Edit\Model\ResourceModel\Post as PostResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Post::class, PostResource::class);
    }
}
