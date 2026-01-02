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

use Magento\Catalog\Controller\Adminhtml\Product\MassStatus;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Neklo\ProductPosition\Model\ResourceModel\Product\Status as ProductStatusResourceModel;
class MassStatusPlugin
{
    protected $collectionFactory;
    protected $statusResource;
    protected $filter;

    public function __construct(
        ProductStatusResourceModel $statusResource,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->statusResource = $statusResource;
        $this->filter = $filter;
    }

    public function afterExecute(MassStatus $subject, ResultInterface $result): ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $status = (int) $subject->getRequest()->getParam('status');

        foreach ($collection as $item) {
            if ($status == Status::STATUS_ENABLED) {
                $categoryIds = $item->getCategoryIds();
                foreach ($categoryIds as $categoryId) {
                    $this->statusResource->checkStatusProduct((int)$categoryId, (int)$item->getId());
                }
            }
        }
        return $result;
    }
}
