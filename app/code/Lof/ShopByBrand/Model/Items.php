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

namespace Lof\ShopByBrand\Model;

use Lof\ShopByBrand\Api\Data\BrandInterface;
use Lof\ShopByBrand\Api\Data\BrandInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Items extends \Ves\Brand\Model\Brand
{
    const ATTRIBUTE_CODE = "product_brand";

    protected $brandDataFactory;

    protected $dataObjectHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Brand\Model\ResourceModel\Brand $resource = null,
        \Ves\Brand\Model\ResourceModel\Brand\Collection $resourceCollection = null,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Ves\Brand\Helper\Data $brandHelper,
        BrandInterfaceFactory $brandDataFactory,
        DataObjectHelper $dataObjectHelper,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_brandHelper = $brandHelper;
        $this->brandDataFactory = $brandDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $productCollectionFactory, $storeManager, $url, $brandHelper, $data);
    }
    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Lof\ShopByBrand\Model\ResourceModel\Items');
    }

    public function getBrandAttributeCode()
    {
        return self::ATTRIBUTE_CODE;
    }

    /**
     * Retrieve brand model with brand data
     * @return BrandInterface
     */
    public function getDataModel()
    {
        $brandData = $this->getData();

        $brandDataObject = $this->brandDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $brandDataObject,
            $brandData,
            BrandInterface::class
        );

        return $brandDataObject;
    }
}
