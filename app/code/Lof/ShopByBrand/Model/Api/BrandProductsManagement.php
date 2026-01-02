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

use Lof\ShopByBrand\Api\Data\BrandProductInterfaceFactory;
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

class BrandProductsManagement implements \Lof\ShopByBrand\Api\BrandProductsManagementInterface
{
    protected $resource;
    protected $brandFactory;
    protected $brandCollectionFactory;
    protected $brandProductFactory;
    protected $extensibleDataObjectConverter;
    protected $dataObjectHelper;

    /**
     * @param ResourceBrand $resource
     * @param BrandFactory $brandFactory
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param BrandProductInterfaceFactory $brandProductFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ResourceBrand $resource,
        BrandFactory $brandFactory,
        BrandCollectionFactory $brandCollectionFactory,
        BrandProductInterfaceFactory $brandProductFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->brandFactory = $brandFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->brandProductFactory = $brandProductFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function get($brandId)
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
    public function getBrandProducts($brand_id)
    {
        $products = $this->resource->getBrandProducts($brand_id);
        $return = [];
        if ($products) {
            foreach ($products as $_product) {
                $_brandproduct = $this->brandProductFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $_brandproduct,
                    $_product,
                    BrandProductInterface::class
                );
                $return[] = $_brandproduct;
            }
            return $return;
        }
        return "Not found any products of the brand.";
    }

    /**
     * {@inheritdoc}
     */
    public function putBrandProducts($brand_id, $products)
    {
        $brand = $this->get($brand_id);
        $message = "Can not add products for the brand!";
        if ($brand) {
            try {
                $products = $this->resource->putBrandProducts($brand_id, $products);
                $message = "Added products to brand successfully!";
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save products for the brand: %1',
                    $exception->getMessage()
                ));
            }
        }
        return $message;
    }
}
