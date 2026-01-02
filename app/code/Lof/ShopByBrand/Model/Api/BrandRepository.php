<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ShopByBrand
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\ShopByBrand\Model\Api;

use Lof\ShopByBrand\Api\BrandRepositoryInterface;
use Lof\ShopByBrand\Api\Data\BrandInterfaceFactory;
use Lof\ShopByBrand\Api\Data\BrandSearchResultsInterfaceFactory;
use Lof\ShopByBrand\Model\BrandFactory;
use Lof\ShopByBrand\Model\ResourceModel\Items as ResourceBrand;
use Lof\ShopByBrand\Model\ResourceModel\Items\CollectionFactory as BrandCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class BrandRepository implements BrandRepositoryInterface
{

    protected $brandCollectionFactory;

    protected $dataObjectProcessor;

    protected $resource;

    protected $extensionAttributesJoinProcessor;

    protected $brandFactory;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $dataBrandFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceBrand $resource
     * @param BrandFactory $brandFactory
     * @param BrandInterfaceFactory $dataBrandFactory
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param BrandSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceBrand $resource,
        BrandFactory $brandFactory,
        BrandInterfaceFactory $dataBrandFactory,
        BrandCollectionFactory $brandCollectionFactory,
        BrandSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->brandFactory = $brandFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBrandFactory = $dataBrandFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lof\ShopByBrand\Api\Data\BrandInterface $brand
    ) {
        /* if (empty($brand->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $brand->setStoreId($storeId);
        } */

        $brandData = $this->extensibleDataObjectConverter->toNestedArray(
            $brand,
            [],
            \Lof\ShopByBrand\Api\Data\BrandInterface::class
        );

        $brandModel = $this->brandFactory->create()->setData($brandData);

        try {
            $this->resource->save($brandModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the brand: %1',
                $exception->getMessage()
            ));
        }
        return $brandModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($brandId)
    {
        $brand = $this->brandFactory->create();
        $this->resource->load($brand, $brandId);
        if (!$brand->getId()) {
            throw new NoSuchEntityException(__('Brand with id "%1" does not exist.', $brandId));
        }
        return $brand->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->brandCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\ShopByBrand\Api\Data\BrandInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\ShopByBrand\Api\Data\BrandInterface $brand
    ) {
        try {
            $brandModel = $this->brandFactory->create();
            $this->resource->load($brandModel, $brand->getBrandId());
            $this->resource->delete($brandModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Brand: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($brandId)
    {
        return $this->delete($this->get($brandId));
    }
}
