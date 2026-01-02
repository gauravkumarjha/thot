<?php

declare(strict_types=1);

namespace Chetaru\Team\Model;

use Magento\Framework\Model\AbstractModel;
use Chetaru\Team\Model\ResourceModel\Post as PostResource;

class Post extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(PostResource::class);
    }
}
