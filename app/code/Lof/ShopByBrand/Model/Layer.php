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

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Layer extends \Magento\Catalog\Model\Layer
{
    protected $_request;

    public function __construct(
        \Magento\Catalog\Model\Layer\ContextInterface $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {

        $this->productCollectionFactory = $productCollectionFactory;
        $this->_request = $request;
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
    }

    /**
     * get brand
     *
     * @return \Lof\ShopByBrand\Model\Items|bool
     */
    public function getBrand()
    {
        $brand_params = $this->_request->getParams();
        $id = $this->_request->getParam('id');
        $brand_id = $this->_request->getParam('brand_id');
        $id = $id?$id:$brand_id;
        if ($id) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->create('Lof\ShopByBrand\Model\Items');
            $attribute_code = \Lof\ShopByBrand\Model\Items::ATTRIBUTE_CODE;
            if (array_key_exists($attribute_code, $brand_params)) {
                $brand = $this->_request->getParam($attribute_code);
                $model->load($brand, 'attribute_option_id');
            } else {
                $model->load($id);
            }
            return $model;
        }
        return false;
    }

    /**
     * get product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections['brand_collection'])) {
            $collection = $this->_productCollections['brand_collection'];
        } else {
            //here you assign your own custom collection of products
            $collection = $this->productCollectionFactory->create();
            $this->prepareProductCollection($collection);
            $this->_productCollections['brand_collection'] = $collection;

            $attribute_code = \Lof\ShopByBrand\Model\Items::ATTRIBUTE_CODE;

            $brand = $this->getBrand();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToSelect('name');
            $collection->addStoreFilter()->addAttributeToFilter($attribute_code, $brand->getAttributeOptionId());

            $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
            $collection->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
        }

        return $collection;
    }
}
