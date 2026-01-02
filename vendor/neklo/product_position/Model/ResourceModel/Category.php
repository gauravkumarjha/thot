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

namespace Neklo\ProductPosition\Model\ResourceModel;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category as CatalogCategory;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status;

class Category extends CatalogCategory
{
    /**
     * Get attached products for category products
     *
     * @param CategoryInterface $category
     * @return array
     */
    public function getAttachedProducts(CategoryInterface $category): array
    {
        $select = $this->getConnection()->select()
            ->from(
                ['category_product' => $this->getCategoryProductTable()],
                []
            )
            ->joinLeft(
                ['product_status' => $this->_resource->getTableName(
                    Status::TABLE_NAME
                )
                ],
                'category_product.product_id = product_status.product_id AND product_status.category_id = ' .
                (int)$category->getId(),
                []
            )
            ->where('category_product.category_id = ' . (int)$category->getId())
            ->columns(
                [
                    'product_id'  => 'category_product.product_id',
                    'is_attached' => 'IF(product_status.is_attached, product_status.is_attached, 0)',
                ]
            );

        return $this->getConnection()->fetchPairs($select);
    }
}
