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

namespace Neklo\ProductPosition\Plugin\Catalog\Controller\Adminhtml\Product;

use Magento\Catalog\Controller\Adminhtml\Product\Save;
use Magento\Catalog\Model\Product\Attribute\Source\Status as SourceStatus;
use Magento\Framework\Controller\ResultInterface;
use Magento\Indexer\Model\IndexerFactory;
use Neklo\ProductPosition\Model\Resolver\Product;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status as ProductStatusResourceModel;
use Neklo\ProductPosition\Model\Stock\StockSourcePool;
use Psr\Log\LoggerInterface;

class SavePlugin
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductStatusResourceModel
     */
    private $statusResource;

    /**
     * @var Product
     */
    private $productResolver;

    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    /**
     * @var StockSourcePool
     */
    private $stockSourcePool;

    /**
     * @param LoggerInterface $logger
     * @param ProductStatusResourceModel $statusResource
     * @param Product $productResolver
     * @param IndexerFactory $indexerFactory
     * @param StockSourcePool $stockSourcePool
     */
    public function __construct(
        LoggerInterface $logger,
        ProductStatusResourceModel $statusResource,
        Product $productResolver,
        IndexerFactory  $indexerFactory,
        StockSourcePool $stockSourcePool
    ) {
        $this->logger = $logger;
        $this->statusResource = $statusResource;
        $this->productResolver = $productResolver;
        $this->indexerFactory = $indexerFactory;
        $this->stockSourcePool = $stockSourcePool;
    }

    /**
     * Save product position
     *
     * @param Save $subject
     * @param ResultInterface $result
     * @return ResultInterface
     * @throws \Exception
     */
    public function afterExecute(Save $subject, ResultInterface $result): ResultInterface
    {
        $product = $subject->getRequest()->getParam('product');
        $productId = $subject->getRequest()->getParam('id');

        if (!$productId) {
            $productId = $this->productResolver->getProduct()->getId();
        }

        $categories = $product['category_ids'] ?? null;

        if (!$this->stockSourcePool->getSource()->isSalable($this->productResolver->getProduct())
            || $product['status'] == SourceStatus::STATUS_DISABLED) {
            $this->statusResource->removeProductFromCategory((int)$productId, $categories);

            return $result;
        }

        if (is_array($categories) && !empty($categories)) {
            try {
                foreach ($categories as $categoryId) {
                    $this->statusResource->checkStatusProduct((int)$categoryId, (int)$productId);
                }
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['exception' => $e]);
            }
        }

        $this->statusResource->removeProductFromCategory((int)$productId, $categories);
        // $this->indexerFactory->create()->load('cataloginventory_stock')->reindexAll();
        // $this->indexerFactory->create()->load('catalog_category_product')->reindexAll();

        return $result;
    }
}
