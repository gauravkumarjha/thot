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

namespace Neklo\ProductPosition\Model\ResourceModel\Product;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Neklo\ProductPosition\Model\Product\StatusFactory;
use Neklo\ProductPosition\Model\Provider\Product;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status\CollectionFactory;

class Status extends AbstractDb
{
    public const TABLE_NAME = 'neklo_productposition_product_status';

    /**
     * @var Product
     */
    private $productProvider;

    /**
     * @var CollectionFactory
     */
    private $statusCollection;

    /**
     * @var StatusFactory
     */
    private $statusFactory;

    /**
     * @var array
     */
    private $updatePositions = [];

    /**
     * @param Context $context
     * @param Status\CollectionFactory $statusCollection
     * @param StatusFactory $statusFactory
     * @param Product $productProvider
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        CollectionFactory $statusCollection,
        StatusFactory $statusFactory,
        Product $productProvider,
        $connectionName = null
    ) {
        $this->statusFactory = $statusFactory;
        $this->productProvider = $productProvider;
        $this->statusCollection = $statusCollection;

        parent::__construct($context, $connectionName);
    }

    /**
     * Status constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }

    /**
     * Add attached products to category
     *
     * @param int $categoryId
     * @param array|null $attachedProduct
     * @return void
     * @throws \Exception
     */
    public function addCategoryAttached(int $categoryId, ?array $attachedProduct): void
    {
        if (is_array($attachedProduct)) {
            foreach ($attachedProduct as $productId => $attached) {
                $status = $this->statusFactory->create();

                $status->setData(
                    [
                        'product_id' => $productId,
                        'category_id' => $categoryId,
                        'is_attached' => $attached,
                    ]
                )->save();
            }
        }
    }

    /**
     * Save product positions in category
     *
     * @param int $categoryId
     * @param array|null $attachedProductList
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function checkCategory(int $categoryId, array $attachedProductList = null): Status
    {
        $newCategoryStatus = false;
        $categoryProducts = [];

        $statusCollection = $this->statusCollection->create()
            ->addFieldToFilter('category_id', ['eq' => $categoryId]);

        $products = $this->productProvider->getProductCollection($categoryId, false);

        if (!$attachedProductList) {
            if (!$statusCollection->getSize()) {
                foreach ($products as $product) {
                    $attachedProductList[$product->getId()] = (int)$product->getIsAttached();
                }

                $this->addCategoryAttached($categoryId, $attachedProductList);

                $newCategoryStatus = true;
            } else {
                foreach ($statusCollection as $status) {
                    $attachedProductList[$status->getProductId()] = (int)$status->getIsAttached();
                }
            }
        } elseif (!$statusCollection->getSize()) {
            $this->addCategoryAttached($categoryId, $attachedProductList);
        }

        foreach ($products as $product) {
            $categoryProducts[$product->getId()] = (int)$product->getIsAttached();
        }

        $newCategoryProducts = array_diff_key($categoryProducts, $attachedProductList);
        if ((count($categoryProducts) !== count($attachedProductList)) || $newCategoryStatus) {
            $this->checkRemovedProductsFromCategory($categoryId, $categoryProducts);
            $this->sortCategory($categoryId, $newCategoryProducts);
        }

        $statusCollection->resetData()->load();

        foreach ($statusCollection as $status) {
            $attachedProduct = $attachedProductList[$status->getProductId()]
                ? $attachedProductList[$status->getProductId()] : false;

            if ((int)$status->getIsAttached() !== (int)$attachedProduct) {
                $status->setIsAttached($attachedProductList[$status->getProductId()]);
                $status->save();
            }
        }

        return $this;
    }

    /**
     * Remove product positions from category
     *
     * @param int $productId
     * @param array|null $categories
     * @return $this
     */
    public function removeProductFromCategory(int $productId, array $categories = null): Status
    {
        if ($categories) {
            $statusCollection = $this->statusCollection->create()
                ->addFieldToFilter('product_id', ['eq' => $productId])
                ->addFieldToFilter('category_id', ['nin' => [$categories]]);
        } else {
            $statusCollection = $this->statusCollection->create()
                ->addFieldToFilter('product_id', ['eq' => $productId]);
        }

        foreach ($statusCollection as $status) {
            $status->delete();
        }

        return $this;
    }

    /**
     * Sort product positions
     *
     * @param int $categoryId
     * @param int $productId
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function checkStatusProduct(int $categoryId, int $productId): Status
    {
        $products = $this->productProvider->getProductCollection($categoryId, false)
            ->setOrder('position', 'ASC')
            ->getData();

        $position = $this->statusCollection->create()
            ->addFieldToFilter('product_id', ['eq' => $productId])
            ->addFieldToFilter('category_id', ['eq' => $categoryId]);

        if ($position->getSize()) {
            $this->checkProductPosition($products, $categoryId, $productId);
            return $this;
        }

        $busyPosition = [];

        $this->addCategoryAttached($categoryId, [$productId => 0]);

        $sortedProducts = [];
        foreach ($products as $iterator => $product) {
            if (!$product['position']) {
                $sortedProducts[$product['entity_id']] = 1;
                continue;
            }

            if ($product['is_attached']) {
                $busyPosition[] = (int)$product['position'];
                unset($products[$iterator]);
                continue;
            }

            $sortedProducts[$product['entity_id']] = (int)$product['position'];
        }

        $this->sortProducts($sortedProducts, $busyPosition)->savePositionProduct($categoryId);

        return $this;
    }

    /**
     * Save product position
     *
     * @param array $products
     * @param int $categoryId
     * @param int $productId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function checkProductPosition(array $products, int $categoryId, int $productId): void
    {
        $neededSort = false;
        foreach ($products as $iterator => $product) {
            $currentPosition = (int)$product['position'];
            if ($product['entity_id'] == $productId) {
                if ($currentPosition == 0) {
                    $this->sortCategory($categoryId);
                    break;
                }

                $nextProduct = $products[$iterator + 1] ?: false;
                $nextPosition = (int)$nextProduct['position'];
                $prevProduct = $iterator > 0 ? $products[$iterator - 1] : false;
                $prevPosition = (int)$prevProduct['position'];

                if ($nextPosition == $currentPosition || $prevPosition == $currentPosition) {
                    $this->updatePositions[$product['entity_id']] = $product['position'] + 1;
                    $neededSort = true;
                }

                continue;
            }

            if ($neededSort) {
                $lastPosition = end($this->updatePositions);
                if ($lastPosition > $currentPosition) {
                    continue;
                }
                $this->updatePositions[$product['entity_id']] = $lastPosition + 1;
            }
        }

        $this->savePositionProduct($categoryId);
    }

    /**
     * Save product position
     *
     * @param int $categoryId
     * @return $this
     */
    private function savePositionProduct(int $categoryId): Status
    {
        $connection = $this->getConnection();

        if (!empty($this->updatePositions)) {
            foreach ($this->updatePositions as $productId => $position) {
                $connection->update(
                    $this->getTable('catalog_category_product'),
                    ['position' => $position],
                    'product_id=' . $productId . ' AND category_id=' . $categoryId
                );
            }

            $this->updatePositions = [];
        }

        return $this;
    }

    /**
     * Sorted category position, if isset new products, add in table product_status
     *
     * @param int $categoryId
     * @param array $newCategoryProducts
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function sortCategory(int $categoryId, array $newCategoryProducts = []): void
    {
        if (!empty($newCategoryProducts)) {
            $this->addCategoryAttached($categoryId, $newCategoryProducts);
        }

        $products = $this->productProvider->getProductCollection($categoryId, false)
            ->setOrder('position', 'ASC')
            ->getData();

        $busyPosition = [];
        $sortedProducts = [];

        $i = 1;
        foreach ($products as $product) {
            if ((int)$product['is_attached'] == 1) {
                $busyPosition[] = (int)$product['position'];

                $i++;

                continue;
            }

            $sortedProducts[$product['entity_id']] = $i;

            $i++;
        }

        $this->sortProducts($sortedProducts, $busyPosition)->savePositionProduct($categoryId);
    }

    /**
     * Sort products
     *
     * @param array $products
     * @param array $busyPosition
     * @return $this
     */
    private function sortProducts(array $products, array $busyPosition): Status
    {
        foreach ($products as $productId => $currentPosition) {
            $newPosition = $this->findPositionProduct((int)$currentPosition, $busyPosition);

            $this->updatePositions[$productId] = $newPosition;

            $busyPosition[] = $newPosition;
        }

        return $this;
    }

    /**
     * Find product position
     *
     * @param int $position
     * @param array $busyPosition
     * @return int
     */
    private function findPositionProduct(int $position, array $busyPosition): int
    {
        $attachedPosition= in_array($position, $busyPosition);
        if ($attachedPosition !== false) {
            $position++;
            $position = $this->findPositionProduct($position, $busyPosition);
        }

        return $position;
    }

    /**
     * Remove products positions from category
     *
     * @param int $categoryId
     * @param array $categoryProducts
     * @return void
     */
    private function checkRemovedProductsFromCategory(int $categoryId, array $categoryProducts): void
    {
        $statusCollection = $this->statusCollection->create()
            ->addFieldToFilter('category_id', ['eq' => $categoryId]);

        foreach ($statusCollection as $status) {
            $checkProduct = array_key_exists($status->getProductId(), $categoryProducts);

            if ($checkProduct === false) {
                $status->delete();
            }
        }
    }
}
