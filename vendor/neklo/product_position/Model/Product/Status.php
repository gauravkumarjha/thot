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

namespace Neklo\ProductPosition\Model\Product;

use Magento\Framework\Model\AbstractModel;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status as ProductStatusResourceModel;

class Status extends AbstractModel
{
    /**
     * Product status constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ProductStatusResourceModel::class);
    }
}
