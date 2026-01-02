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

namespace Neklo\ProductPosition\Model\Provider;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Neklo\ProductPosition\Model\Config;
use Neklo\ProductPosition\Model\Resolver\Category;
use Neklo\ProductPosition\Model\Resolver\Store;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status as ProductStatus;
use Neklo\ProductPosition\Model\Stock\StockSourcePool;

class Product
{
    /**
     * @var Stock
     */
    private $stockFilter;

    /**
     * @var CollectionFactory
     */
    private $productCollection;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Store
     */
    private $storeResolver;

    /**
     * @var Category
     */
    private $categoryResolver;

    /**
     * @var StockSourcePool
     */
    private $stockSourcePool;

    /**
     * @param Stock $stockFilter
     * @param CollectionFactory $productCollectionFactory
     * @param Config $config
     * @param Store $storeResolver
     * @param Category $categoryResolver
     * @param StockSourcePool $stockSourcePool
     */
    public function __construct(
        Stock $stockFilter,
        CollectionFactory $productCollectionFactory,
        Config $config,
        Store $storeResolver,
        Category $categoryResolver,
        StockSourcePool $stockSourcePool
    ) {
        $this->stockFilter = $stockFilter;
        $this->productCollection = $productCollectionFactory;
        $this->config = $config;
        $this->storeResolver = $storeResolver;
        $this->categoryResolver = $categoryResolver;
        $this->stockSourcePool = $stockSourcePool;
    }

    /**
     * Get collection
     *
     * @param int $page
     * @param int $count
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCollection(int $page = 1, int $count = 20): array
    {
        $collection = $this->getProductCollection(null);
        $collection->setPageSize($count);

        if ($collection->getLastPageNumber() >= $page) {
            $collection->setCurPage($page);
            $collection->setOrder('position', 'ASC');
        } else {
            return [];
        }

        return $collection->getItems();
    }

    /**
     * Get product collection
     *
     * @param null|int $categoryId
     * @param bool $stockFilter
     * @return Collection
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getProductCollection(?int $categoryId = null, bool $stockFilter = true): Collection
    {
        if (null === $categoryId) {
            $categoryId = $this->getCategory()->getId();
        }

        /** Add get default store view website id for correct sql query in product collection */
        $store = $this->getStore();

        if ($store->getWebsiteId() == 0) {
            $stores = $this->storeResolver->getStores();
            $store = reset($stores);
        }

        $collection = $this->productCollection->create()
            ->addStoreFilter($store->getId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('thumbnail')
            ->joinField(
                "position",
                "catalog_category_product",
                "position",
                "product_id = entity_id",
                "category_id = {$categoryId}",
                "inner"
            )
            ->addAttributeToFilter(
                'visibility',
                [
                    'in' => [
                        Visibility::VISIBILITY_IN_CATALOG,
                        Visibility::VISIBILITY_BOTH,
                    ]
                ]
            )
            ->addAttributeToFilter(
                'status',
                Status::STATUS_ENABLED
            )
            ->addPriceData(null, $store->getWebsiteId());

        if ($stockFilter) {
            $condition = [
                '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=1',
                '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0',
            ];

            $this->stockFilter->addIsInStockFilterToCollection($collection);

            if ($this->config->isManageStock() && !$this->config->isShowOutOfStock()) {
                $condition[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=1';
            } else {
                $condition[] = '{{table}}.use_config_manage_stock = 1';
            }

            $collection->joinField(
                'inventory_in_stock',
                'cataloginventory_stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '(' . implode(') OR (', $condition) . ')'
            );
        }

        $collection->getSelect()
            ->joinLeft(
                [
                    'product_status' => $collection->getResource()
                        ->getTable(ProductStatus::TABLE_NAME)
                ],
                'e.entity_id = product_status.product_id AND product_status.category_id = ' . (int)$categoryId,
                [
                    'is_attached' => 'product_status.is_attached'
                ]
            );

        //var_dump($collection->getSelect()->__toString());

        return $collection;
    }

    /**
     * Get store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore(): StoreInterface
    {
        return $this->storeResolver->getStore();
    }

    /**
     * Get category
     *
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function getCategory(): CategoryInterface
    {
        return $this->categoryResolver->getCategory();
    }
}
