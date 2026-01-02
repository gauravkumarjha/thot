<?php

declare(strict_types=1);

namespace Chetaru\Edit\Model;

use Magento\Framework\Model\AbstractModel;
use Chetaru\Edit\Model\ResourceModel\Post as PostResource;

class Post extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(PostResource::class);
    }
}
