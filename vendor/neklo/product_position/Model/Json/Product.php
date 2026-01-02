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

namespace Neklo\ProductPosition\Model\Json;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Neklo\ProductPosition\Model\Provider\Product as ProductProvider;
use Neklo\ProductPosition\Model\Resolver\Category;
use Neklo\ProductPosition\Model\Resolver\Store;
use Neklo\ProductPosition\Model\ResourceModel\Category as CategoryFactory;
use Neklo\ProductPosition\Model\Stock\StockSourcePool;

class Product
{
    /**
     * @var Data
     */
    private $pricingHelper;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var Store
     */
    private $storeResolver;

    /**
     * @var Category
     */
    private $categoryResolver;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var StockSourcePool
     */
    private $stockSourcePool;

    /**
     * @var array
     */
    private $mapFields = [
        'entity_id',
        'name',
        'sku',
        'price',
        'is_attached',
        'min_price',
        'max_price',
        'position',
    ];

    /**
     * @param Data $pricingHelper
     * @param Image $imageHelper
     * @param CategoryFactory $categoryFactory
     * @param Store $storeResolver
     * @param Category $categoryResolver
     * @param ProductProvider $productProvider
     * @param SerializerInterface $serializer
     * @param StockSourcePool $stockSourcePool
     */
    public function __construct(
        Data $pricingHelper,
        Image $imageHelper,
        CategoryFactory $categoryFactory,
        Store $storeResolver,
        Category $categoryResolver,
        ProductProvider $productProvider,
        SerializerInterface $serializer,
        StockSourcePool $stockSourcePool
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->imageHelper = $imageHelper;
        $this->categoryFactory = $categoryFactory;
        $this->storeResolver = $storeResolver;
        $this->categoryResolver = $categoryResolver;
        $this->productProvider = $productProvider;
        $this->serializer = $serializer;
        $this->stockSourcePool = $stockSourcePool;
    }

    /**
     * Get attached product as json
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttachedProductsJson(): string
    {
        $products = $this->categoryFactory->getAttachedProducts($this->getCategory());

        if (count($products) === 0) {
            return '{}';
        }

        return $this->serializer->serialize($products);
    }

    /**
     * Get sorted product positions as json
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getSortedProductsPositionJson(): string
    {
        $collection = $this->productProvider
            ->getProductCollection((int)$this->getCategory()->getId())
            ->setOrder('position', 'ASC');

        $products = [];
        foreach ($collection as $product) {
            $products[$product->getId()] = $product->getData();
        }

        if (count($products) === 0) {
            return '{}';
        }

        $productIdList = array_keys($products);
        $productPositionList = range(1, count($productIdList));
        $products = array_combine($productIdList, $productPositionList);

        return $this->serializer->serialize($products);
    }

    /**
     * Get collection data
     *
     * @param int $page
     * @param int $count
     * @param bool $asArray
     * @return array|string
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getCollectionData(int $page = 1, int $count = 20, bool $asArray = false)
    {
        $productDataList = [];

        $collection = $this->productProvider->getCollection($page, $count);

        $startPosition = $count * $page - $count + 1;

        foreach ($collection as $product) {
            $productData = $product->toArray($this->mapFields);

            $productData['image'] = $this->imageHelper
                ->init($product, 'neklo_product_position_thumbnail')
                ->getUrl();

            $storeId = $this->getStore()->getId();

            if ((int)$productData['price'] == 0) {
                $minPrice = $this->pricingHelper->currencyByStore(
                    $productData['min_price'],
                    $storeId,
                    true,
                    false
                );

                $maxPrice = $this->pricingHelper->currencyByStore(
                    $productData['max_price'],
                    $storeId,
                    true,
                    false
                );

                $productData['price'] = "{$minPrice}-{$maxPrice}";
            } else {
                $productData['price'] = $this->pricingHelper->currencyByStore(
                    $productData['price'],
                    $storeId,
                    true,
                    false
                );
            }

            $productData['position'] = $startPosition;
            $productData['attached'] = (bool)$product['is_attached'];

            $productData['status'] = $this->stockSourcePool->getSource()->isSalable($product) ?
                __('In Stock') : __('Out of Stock');

            $productDataList[] = $productData;

            $startPosition++;
        }

        return $asArray ? $productDataList : $this->serializer->serialize($productDataList);
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
