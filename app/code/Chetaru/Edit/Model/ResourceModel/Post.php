<?php

declare(strict_types=1);

namespace Chetaru\Edit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Post extends AbstractDb
{
    private const TABLE_NAME = 'chetaru_edit_post';
    private const PRIMARY_KEY = 'post_id';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }
}
