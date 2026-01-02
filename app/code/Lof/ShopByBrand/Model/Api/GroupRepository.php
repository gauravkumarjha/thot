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

use Lof\ShopByBrand\Api\Data\GroupInterfaceFactory;
use Lof\ShopByBrand\Api\Data\GroupSearchResultsInterfaceFactory;
use Lof\ShopByBrand\Api\GroupRepositoryInterface;
use Lof\ShopByBrand\Model\GroupFactory;
use Lof\ShopByBrand\Model\ResourceModel\Group as ResourceGroup;
use Lof\ShopByBrand\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class GroupRepository implements GroupRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $dataGroupFactory;

    protected $resource;

    protected $extensionAttributesJoinProcessor;

    protected $groupFactory;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $groupCollectionFactory;


    /**
     * @param ResourceGroup $resource
     * @param GroupFactory $groupFactory
     * @param GroupInterfaceFactory $dataGroupFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param GroupSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceGroup $resource,
        GroupFactory $groupFactory,
        GroupInterfaceFactory $dataGroupFactory,
        GroupCollectionFactory $groupCollectionFactory,
        GroupSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->groupFactory = $groupFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGroupFactory = $dataGroupFactory;
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
        \Lof\ShopByBrand\Api\Data\GroupInterface $group
    ) {
        /* if (empty($group->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $group->setStoreId($storeId);
        } */

        $groupData = $this->extensibleDataObjectConverter->toNestedArray(
            $group,
            [],
            \Lof\ShopByBrand\Api\Data\GroupInterface::class
        );

        $groupModel = $this->groupFactory->create()->setData($groupData);

        try {
            $this->resource->save($groupModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the group: %1',
                $exception->getMessage()
            ));
        }
        return $groupModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($groupId)
    {
        $group = $this->groupFactory->create();
        $this->resource->load($group, $groupId);
        if (!$group->getId()) {
            throw new NoSuchEntityException(__('Group with id "%1" does not exist.', $groupId));
        }
        return $group->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->groupCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\ShopByBrand\Api\Data\GroupInterface::class
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
        \Lof\ShopByBrand\Api\Data\GroupInterface $group
    ) {
        try {
            $groupModel = $this->groupFactory->create();
            $this->resource->load($groupModel, $group->getGroupId());
            $this->resource->delete($groupModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Group: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groupId)
    {
        return $this->delete($this->get($groupId));
    }
}
