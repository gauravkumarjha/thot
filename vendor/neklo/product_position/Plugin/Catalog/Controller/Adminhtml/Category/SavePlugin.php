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

namespace Neklo\ProductPosition\Plugin\Catalog\Controller\Adminhtml\Category;

use Magento\Catalog\Controller\Adminhtml\Category\Save;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Indexer\Model\IndexerFactory;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status;
use Psr\Log\LoggerInterface;

class SavePlugin
{
    /**
     * @var Status
     */
    private $statusResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var IndexerFactory
     */
    private $indexerFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param Status $statusResource
     * @param LoggerInterface $logger
     * @param IndexerFactory $indexerFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Status $statusResource,
        LoggerInterface $logger,
        IndexerFactory $indexerFactory,
        SerializerInterface $serializer
    ) {
        $this->statusResource = $statusResource;
        $this->logger = $logger;
        $this->indexerFactory = $indexerFactory;
        $this->serializer = $serializer;
    }

    /**
     * Save product positions
     *
     * @param Save $subject
     * @param ResultInterface $result
     * @return ResultInterface
     * @throws \Exception
     */
    public function afterExecute(Save $subject, ResultInterface $result): ResultInterface
    {
        $categoryId = $subject->getRequest()->getParam('entity_id');
        $attachJson = $subject->getRequest()->getParam('attached_category_products', []);

        $attachedProducts = is_array($attachJson) ? $attachJson : $this->serializer->unserialize($attachJson);

        try {
            $this->statusResource->checkCategory((int)$categoryId, $attachedProducts);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
        }

      //  $this->indexerFactory->create()->load('catalog_category_product')->reindexAll();

        return $result;
    }
}
