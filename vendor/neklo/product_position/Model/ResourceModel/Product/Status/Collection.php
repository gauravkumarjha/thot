<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the NekloEULA that is bundled with this package in the file LICENSE.txt.
 *
 * It is also available through the world-wide-web at this URL: http://store.neklo.com/LICENSE.txt
 *
 * Copyright (c)  Neklo (http://store.neklo.com/)
 */

declare(strict_types=1);

namespace Neklo\ProductPosition\Model\ResourceModel\Product\Status;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Neklo\ProductPosition\Model\Product\Status;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status as ProductStatusResourceModel;

class Collection extends AbstractCollection
{
    /**
     * Collection constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            Status::class,
            ProductStatusResourceModel::class
        );
    }
}
