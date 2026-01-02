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

namespace Neklo\ProductPosition\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Neklo\ProductPosition\Model\Json\Product as ProductJson;
use Neklo\ProductPosition\Model\Provider\Product as ProductProvider;

class Product implements ArgumentInterface
{
    /**
     * @var ProductJson
     */
    private $productJson;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @param ProductJson $productJson
     * @param ProductProvider $productProvider
     */
    public function __construct(
        ProductJson $productJson,
        ProductProvider $productProvider
    ) {
        $this->productJson = $productJson;
        $this->productProvider = $productProvider;
    }

    /**
     * Get attached products in json
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttachedProductsJson(): string
    {
        try {
            return $this->productJson->getAttachedProductsJson();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get sorted products positions in json
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getSortedProductsPositionJson(): string
    {
        try {
            return $this->productJson->getSortedProductsPositionJson();
        } catch (\Exception $e) {
            return '';
        }
    }
    /**
     * Get collection size
     *
     * @return int
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCollectionSize(): int
    {
        try {
            return count($this->productProvider->getProductCollection());
        } catch (\Exception $e) {
            return 0;
        }
    }
}
