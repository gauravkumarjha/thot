<?php

declare(strict_types=1);

namespace Chetaru\Article\Model;

use Magento\Framework\Model\AbstractModel;
use Chetaru\Article\Model\ResourceModel\Post as PostResource;

class Post extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(PostResource::class);
    }
}
