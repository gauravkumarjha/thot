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
namespace Lof\ShopByBrand\Helper;

class Attribute extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;

    protected $registry;
    protected $_eavSetupFactory;
    protected $_storeManager;
    protected $_attributeFactory;
    protected $_brandFactory;

    /**
     * @var array
     */
    protected $attributeValues;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
     */
    protected $tableFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Ves\Brand\Model\BrandFactory $brandFactory,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory
    ) {
        parent::__construct($context);

        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_storeManager = $storeManager;
        $this->_attributeFactory = $attributeFactory;
        $this->_brandFactory = $brandFactory;
        $this->attributeRepository = $attributeRepository;
        $this->tableFactory = $tableFactory;
    }

    /**
     * Sync attribute for brand item
     */
    public function syncAttributeForBrand($brandId, $brand = null)
    {
        $attribute_code = \Lof\ShopByBrand\Model\Items::ATTRIBUTE_CODE;
        $allStores = $this->_storeManager->getStores();
        $attributeInfo=$this->_attributeFactory->getCollection()
            ->addFieldToFilter('attribute_code', ['eq'=>$attribute_code])
            ->getFirstItem();

        $attribute_id = $attributeInfo->getAttributeId();
        $attribute_options = $this->getAttributeOptions($attribute_id);
        $options = [];
        $options['attribute_id'] = $attribute_id;
        $brand_items = [];
        $is_create_new = false;
        if (!$brand) {
            $brand = $this->_brandFactory->create()->load((int)$brandId);
        }
        $brand_attribute_id = $brand->getAttributeId();
        $brand_items[$brand->getName()] = ["id" => $brand->getId(), "attribute_id" => $brand_attribute_id];
        if (!$brand_attribute_id || !isset($attribute_options[$brand_attribute_id])) {
            $option_id = $this->getOptionId($attribute_code, $brand->getName());
            if (!$option_id) {
                $options['value'][$brand->getName()][0]=$brand->getName();
                foreach ($allStores as $store) {
                    if (0 < $store->getId()) {
                        $options['value'][$brand->getName()][$store->getId()] = $brand->getName();
                    }
                }
            }
        } elseif ($brand_attribute_id && isset($attribute_options[$brand_attribute_id]) && $attribute_options[$brand_attribute_id] != $brand->getName()) {
            //update label of attribute option
            $this->updateOptionLabel($brand_attribute_id, $brand->getName());
        }
        if (isset($options['value']) && $options['value']) {
            $eavSetup = $this->_eavSetupFactory->create();
            $eavSetup->addAttributeOption($options);
            $is_create_new = true;
        }

        $is_create_new = true;
        $_brand_model = null;
        if ($is_create_new) {
            $new_attribute_options = $this->getAttributeOptions($attribute_id);
            foreach ($new_attribute_options as $optionId => $optionLabel) {
                if ($optionId) {
                    if (isset($brand_items[$optionLabel])) {
                        $_brand_model = $this->_brandFactory->create()->load((int)$brand_items[$optionLabel]["id"]);
                        $_brand_model->setAttributeId($optionId);
                        $_brand_model->setData("products", null);
                        try {
                            $_brand_model->save();
                        } catch (\Exception $e) {
                            //$this->messageManager->addError($e->getMessage());
                            continue;
                        }
                    } else {
                        //Create new ves brand item function will write at here: option id, option label, store id = 0
                    }
                }
            }
        }
        if (!$_brand_model) {
            $_brand_model = $brand;
        }
        $this->updateAttributeOption($brandId, $_brand_model);
    }

    /**
     * update attribute option for brand item
     *
     * @param int $brandId
     * @param mixed|null $_brand_model
     * @return void
     */
    protected function updateAttributeOption($brandId, $_brand_model = null)
    {
        $model = $this->_attributeFactory->setEntityTypeId(
            \Magento\Catalog\Model\Product::ENTITY
        );
        $attribute_code = \Lof\ShopByBrand\Model\Items::ATTRIBUTE_CODE;
        $model->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $attribute_code);
        if (!$_brand_model) {
            $_brand_model = $this->_brandFactory->create()->load((int)$brandId);
        }
        if ($_brand_model && $_brand_model->getId()) {
            $attribute_options = [];
            foreach ($model->getOptions() as $option) {
                $optionValue = (int)$option->getValue();
                $optionLabel = $option->getLabel();
                if ($optionValue && $optionLabel) {
                    $attribute_options[$optionLabel] = $optionValue;
                }
            }
            if ($attribute_options) {
                if (isset($attribute_options[$_brand_model->getName()]) && $attribute_options[$_brand_model->getName()]) {
                    $_brand_model->setAttributeOptionId((int)$attribute_options[$_brand_model->getName()]);
                    $_brand_model->setData("products", null);
                    try {
                        $_brand_model->save();
                    } catch (\Exception $e) {
                        //
                    }
                }
            }
        }
    }

    /**
     * get attribute options
     *
     * @param int $attribute_id
     * @return mixed
     */
    protected function getAttributeOptions($attribute_id)
    {
        $model = $this->_brandFactory->create();
        $connection = $model->getResource()->getConnection();
        $select = $connection->select()->from(
            ["main_table" => $model->getResource()->getTable('eav_attribute_option')],
            'option_id'
        )
        ->joinLeft(
            ['eav_option_value'=>$model->getResource()->getTable('eav_attribute_option_value')],
            "main_table.option_id = eav_option_value.option_id and eav_option_value.store_id=0",
            ["option_value" => "eav_option_value.value"]
        )->where('attribute_id = '.$attribute_id);
        $options = $connection->fetchAll($select);
        $return_options = [];
        if ($options) {
            foreach ($options as $_option) {
                $return_options[$_option['option_id']] = $_option["option_value"];
            }
        }
        return $return_options;
    }

    /**
     * Get attribute by code.
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     */
    protected function getAttribute($attributeCode)
    {
        if (!isset($this->_attributeRepositoryData)) {
            $this->_attributeRepositoryData = $this->attributeRepository->get($attributeCode);
        }
        return $this->_attributeRepositoryData;
    }

    /**
     * Update option label
     *
     * @param int|string $optionId
     * @param string $optionLabel
     * @return mixed
     */
    protected function updateOptionLabel($optionId, $optionLabel)
    {
        $model = $this->_brandFactory->create();
        $connection = $model->getResource()->getConnection();
        $data = ["value" => $optionLabel];
        $where = ['option_id = ?' => (int)$optionId];
        $tableName = $connection->getTableName('eav_attribute_option_value');
        $updatedRows = $connection->update($tableName, $data, $where);
        return $updatedRows;
    }

    /**
     * Delete option by id
     *
     * @param int|string $optionId
     * @return mixed
     */
    protected function deleteOptionById($optionId)
    {
        $model = $this->_brandFactory->create();
        $connection = $model->getResource()->getConnection();
        $where = ['option_id = ?' => (int)$optionId];
        $tableNameValue = $connection->getTableName('eav_attribute_option_value');
        $tableNameOption = $connection->getTableName('eav_attribute_option');
        $deleteRows = $connection->delete($tableNameValue, $where);
        $deleteRows = $connection->delete($tableNameOption, $where);
        return $deleteRows;
    }

    /**
     * Find the ID of an option matching $label, if any.
     *
     * @param string $attributeCode Attribute code
     * @param string $label Label to find
     * @param bool $force If true, will fetch the options even if they're already cached.
     * @return int|false
     */
    protected function getOptionId($attributeCode, $label, $force = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];

            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }

    /**
     * Process sync brand attribute
     *
     * @param mixed|null $filters
     * @return void
     */
    public function processSyncBrandAttribute($filters = null)
    {
        $collection = $this->_brandFactory->create()->getCollection();
        if ($collection->count()) {
            $attribute_code = \Lof\ShopByBrand\Model\Items::ATTRIBUTE_CODE;
            $allStores = $this->_storeManager->getStores();
            $attributeInfo=$this->_attributeFactory->getCollection()
               ->addFieldToFilter('attribute_code', ['eq'=>$attribute_code])
               ->getFirstItem();

            $attribute_id = $attributeInfo->getAttributeId();
            $attribute_options = $this->getAttributeOptions($attribute_id);
            $options = [];
            $options['attribute_id'] = $attribute_id;
            $brand_items = [];
            $is_create_new = false;
            foreach ($collection as $brand) {
                $brand_attribute_id = $brand->getAttributeId();
                $brand_items[$brand->getName()] = ["id" => $brand->getId(), "attribute_id" => $brand_attribute_id];
                if (!$brand_attribute_id || !isset($attribute_options[$brand_attribute_id])) {
                    $option_id = $this->getOptionId($attribute_code, $brand->getName());
                    if (!$option_id) {
                        $options['value'][$brand->getName()][0]=$brand->getName();
                        foreach ($allStores as $store) {
                            if (0 < $store->getId()) {
                                $options['value'][$brand->getName()][$store->getId()] = $brand->getName();
                            }
                        }
                    }
                } elseif ($brand_attribute_id && isset($attribute_options[$brand_attribute_id]) && $attribute_options[$brand_attribute_id] != $brand->getName()) {
                    //update label of attribute option
                    $this->updateOptionLabel($brand_attribute_id, $brand->getName());
                }
            }
            if (isset($options['value']) && $options['value']) {
                $eavSetup = $this->_eavSetupFactory->create();
                $eavSetup->addAttributeOption($options);
                $is_create_new = true;
            }

            $is_create_new = true;
            if ($is_create_new) {
                $new_attribute_options = $this->getAttributeOptions($attribute_id);
                foreach ($new_attribute_options as $optionId => $optionLabel) {
                    if ($optionId) {
                        if (isset($brand_items[$optionLabel])) {
                            $_brand_model = $this->_brandFactory->create()->load((int)$brand_items[$optionLabel]["id"]);
                            $_brand_model->setAttributeId($optionId);
                            $_brand_model->setData("products", null);
                            try {
                                $_brand_model->save();
                            } catch (\Exception $e) {
                                //$this->messageManager->addError($e->getMessage());
                                continue;
                            }
                        } else {
                            //Create new ves brand item function will write at here: option id, option label, store id = 0
                        }
                    }
                }
            }
            $this->updateAttributeOptionAll();
        }
    }

    /**
     * update attribute option all
     * @return void
     */
    protected function updateAttributeOptionAll()
    {
        $model = $this->_attributeFactory->setEntityTypeId(
            \Magento\Catalog\Model\Product::ENTITY
        );
        $attribute_code = \Lof\ShopByBrand\Model\Items::ATTRIBUTE_CODE;
        $model->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $attribute_code);
        $collection = $this->_brandFactory->create()->getCollection();
        if ($collection->count()) {
            $attribute_options = [];
            foreach ($model->getOptions() as $option) {
                $optionValue = (int)$option->getValue();
                $optionLabel = $option->getLabel();
                if ($optionValue && $optionLabel) {
                    $attribute_options[$optionLabel] = $optionValue;
                }
            }
            if ($attribute_options) {
                foreach ($collection as $brand) {
                    if (isset($attribute_options[$brand->getName()]) && $attribute_options[$brand->getName()]) {
                        $_brand_model = $this->_brandFactory->create()->load((int)$brand->getId());
                        $_brand_model->setAttributeOptionId((int)$attribute_options[$brand->getName()]);
                        $_brand_model->setData("products", null);
                        try {
                            $_brand_model->save();
                        } catch (\Exception $e) {
                            //
                        }
                    }
                }
            }
        }
    }
}
