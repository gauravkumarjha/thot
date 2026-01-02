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

namespace Neklo\ProductPosition\Model\Stock\Source;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class Multi implements StockSourceInterface
{
    public const SOURCE_TYPE = 'multi';

    public const TYPE_WEBSITE = 'website';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Request $request
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Request $request
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->request = $request;

        // avoid compilation error in case inventory modules were removed
        if (interface_exists(\Magento\InventorySalesApi\Api\StockResolverInterface::class)) {
            $this->stockResolver = $this->objectManager->get(StockResolverInterface::class);
            $this->isProductSalable = $this->objectManager->get(IsProductSalableInterface::class);
            $this->getSkusByProductIds = $this->objectManager->get(GetSkusByProductIdsInterface::class);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function isSalable(ProductInterface $product): bool
    {
        $productId = $product->getId();

        $storeId = $this->request->getParam('store');
        $store = $this->storeManager->getStore($storeId);

        $websiteCode = $this->storeManager->getWebsite($store->getWebsiteId())->getCode();
        $stockId = $this->stockResolver->execute(self::TYPE_WEBSITE, $websiteCode)->getStockId();

        $sku = $this->getSkusByProductIds->execute([$productId])[$productId];

        return $this->isProductSalable->execute($sku, $stockId);
    }
}
