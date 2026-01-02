<?php

namespace DiMedia\ProductPostions\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ProductCollection
{
    public function beforeLoad(Collection $subject)
    {
        // $select = $subject->getSelect();

        // // Ensure 'stock' table is not already joined
        // if (!isset($select->getPart(\Zend_Db_Select::FROM)['stock'])) {
        //     $select->joinLeft(
        //         ['stock' => $subject->getTable('cataloginventory_stock_status')],
        //         'e.entity_id = stock.product_id',
        //         ['stock_status']
        //     );
        // }

        // // Sort: In-stock first, Out-of-stock last, then by category position
        // $select->order(new \Zend_Db_Expr("stock.stock_status DESC, cat_index.position ASC"));

        return [$subject];
    }
}
