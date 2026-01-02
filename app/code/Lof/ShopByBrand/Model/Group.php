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

namespace Lof\ShopByBrand\Model;

use Lof\ShopByBrand\Api\Data\GroupInterface;
use Lof\ShopByBrand\Api\Data\GroupInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Group extends \Ves\Brand\Model\Group
{

    protected $groupDataFactory;

    protected $dataObjectHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\ShopByBrand\Model\ResourceModel\Group $resource = null,
        \Lof\ShopByBrand\Model\ResourceModel\Group\Collection $resourceCollection = null,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        GroupInterfaceFactory $groupDataFactory,
        DataObjectHelper $dataObjectHelper,
        array $data = []
    ) {
        $this->groupDataFactory = $groupDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $productCollectionFactory, $storeManager, $url, $scopeConfig, $data);
    }

    /**
     * Retrieve group model with group data
     * @return GroupInterface
     */
    public function getDataModel()
    {
        $groupData = $this->getData();

        $groupDataObject = $this->groupDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $groupDataObject,
            $groupData,
            GroupInterface::class
        );

        return $groupDataObject;
    }
}
