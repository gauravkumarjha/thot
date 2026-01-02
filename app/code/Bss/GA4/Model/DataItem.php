<?php
declare(strict_types=1);
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at thisURL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GA4\Model;

use Bss\GA4\Helper\Data;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataItem
{
    /**
     * @var int
     */
    protected $attributeCodeIdName = 0;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var []
     */
    protected $listCategories;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var Attribute
     */
    protected $attribute;

    /**
     * @var bool
     */
    public $setItemVariant = true;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CollectionFactory $collectionFactory
     * @param Data $data
     * @param Config $config
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     * @param Attribute $attribute
     * @param Session $checkoutSession
     */
    public function __construct(
        CategoryRepositoryInterface                                     $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Bss\GA4\Helper\Data                                            $data,
        \Bss\GA4\Model\Config                                           $config,
        \Magento\Catalog\Api\ProductRepositoryInterface                 $productRepository,
        \Magento\Store\Model\StoreManagerInterface                      $storeManager,
        \Magento\Framework\App\ResourceConnection                       $resource,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute              $attribute,
        \Magento\Checkout\Model\Session                                 $checkoutSession
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->data = $data;
        $this->productRepository = $productRepository;
        $this->config = $config;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        $this->attribute = $attribute;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Render item data
     *
     * @param mixed $product
     * @param int $index
     * @return array
     * @throws NoSuchEntityException|LocalizedException|\Zend_Db_Statement_Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function renderItem($product, $index = 0)
    {
        $storeId = $this->storeManager->getStore()->getId();
        if ($product instanceof \Magento\Sales\Model\Order\Creditmemo\Item) {
            $product = $this->productRepository->getById($product->getProductId(), false, (string)$storeId);
        }
        $typeProductIdentifier = $this->config->getItemId();
        $productIdentifier = $this->getProductIdentifier($product, $typeProductIdentifier);
        $productModel = $this->getProductModel($product);

        $itemBrand = '';
        if ($this->config->getItemBrand()) {
            $productId = $productModel->getId();
            if ($product->getProductType() == "configurable" && isset($product->getChildren()[0])) {
                $productId = $product->getChildren()[0]->getProductId();
            }
            $itemBrand = $this->getValueAttribute($productId, $this->config->getItemBrand());
        }
        $itemAffiliation = '';
        if ($this->config->getItemAffiliation()) {
            $productId = $productModel->getId();
            if ($product->getProductType() == "configurable" && isset($product->getChildren()[0])) {
                $productId = $product->getChildren()[0]->getProductId();
            }
            $itemAffiliation = $this->getValueAttribute($productId, $this->config->getItemAffiliation());
        }
        if ($product instanceof Item ||
            $product instanceof \Magento\Sales\Model\Order\Item ||
            $product instanceof \Magento\Quote\Model\Quote\Address\Item
        ) {
            $baseInfo = $this->getProductBaseInfo($product->getProduct());
            $discount = $product->getDiscountAmount();
        } else {
            $baseInfo = $this->getProductBaseInfo($product);
            $discount = $baseInfo['discount'];
        }
        $itemCategory = $baseInfo['itemCategory'];
        $price = $baseInfo['price'];
        $dataGA4 = [
            "item_id" => $productIdentifier,
            "item_name" => $product->getName(),
            "discount" => $discount ? (float)$discount : 0,
            "affiliation" => $itemAffiliation,
            "currency" => $this->data->getCurrency(),
            "index" => $product->getCatIndexPosition() > 0 ? $product->getCatIndexPosition() + 1 : $index,
            "item_brand" => $itemBrand,
            "item_category" => isset($itemCategory[0]) ? $itemCategory[0] : '',
            "item_category2" => isset($itemCategory[1]) ? $itemCategory[1] : '',
            "item_category3" => isset($itemCategory[2]) ? $itemCategory[2] : '',
            "item_category4" => isset($itemCategory[3]) ? $itemCategory[3] : '',
            "item_category5" => isset($itemCategory[4]) ? $itemCategory[4] : '',
            "item_variant" => '',
            "price" => round((float)$price, 5),
            "item_list_id" => $product->getId(),
            "item_list_name" => $product->getName(),
            "quantity" => $baseInfo["qty"]
        ];
        if ($this->setItemVariant) {
            if ($productModel->getTypeId() == "configurable") {
                $options = $productModel->getTypeInstance()->getConfigurableAttributesAsArray($productModel);
                $variant = [];
                foreach ($options as $option) {
                    $label = [];
                    foreach ($option['values'] as $value) {
                        $label[] = $value['store_label'];
                    }
                    $variant[] = $option['store_label'] . ':' . implode(',', $label);
                }
                $dataGA4 ["item_variant"] = count($variant) ? implode(',', $variant) : '';
            }
        }
        return $dataGA4;
    }

    /**
     * Get bundle price
     *
     * @param mixed $product
     * @return float|int|mixed
     */
    public function getPriceInfo($product)
    {
        if ($product->getTypeId() != "bundle") {
            return $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
        }
        $typePrice = $this->config->getConfigTypePriceBundle();
        $min = $product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue();
        $max = $product->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
        switch ($typePrice) {
            case 'min':
                $price = $min;
                break;
            case 'avg':
                $price = ($min + $max) / 2;
                break;
            case 'max':
                $price = $max;
                break;
            default:
                $price = $min;
        }
        return $price;
    }

    /**
     * Get product identifier
     *
     * @param mixed $product
     * @param string $typeProductIdentifier
     * @return int|mixed|string
     */
    public function getProductIdentifier($product, $typeProductIdentifier)
    {
        if ($typeProductIdentifier == 'id') {
            if (!$product instanceof Item &&
                !$product instanceof \Magento\Sales\Model\Order\Item &&
                !$product instanceof \Magento\Quote\Model\Quote\Address\Item) {
                $productIdentifier = $product->getId();
            } else {
                $productIdentifier = $product->getProduct()->getId();
            }
        } else {
            if (!$product instanceof Item &&
                !$product instanceof \Magento\Sales\Model\Order\Item &&
                !$product instanceof \Magento\Quote\Model\Quote\Address\Item) {
                $productIdentifier = $product->getSku();
            } else {
                $productIdentifier = $product->getProduct()->getSku();
            }
        }
        return $productIdentifier;
    }

    /**
     * Get value attribute
     *
     * @param int $productId
     * @param string $attribute
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getValueAttribute($productId, $attribute)
    {
        $product = $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());
        if ($product->getTypeId() == "configurable") {
            $label = [];
            $productAttributeOptions = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
            foreach ($productAttributeOptions as $key => $value) {
                if ($value['attribute_code'] == $attribute) {
                    foreach ($value['values'] as $option) {
                        $label[] = $option['store_label'];
                    }
                }
            }
            if ($label) {
                return implode(',', $label);
            }
        }
        return $product->getResource()
            ->getAttribute($attribute)
            ->getFrontend()->getValue($product) ?: '';
    }

    /**
     * Get category name
     *
     * @param array $categoryIds
     * @return array|string
     * @throws NoSuchEntityException|LocalizedException|\Zend_Db_Statement_Exception
     */
    public function getCategoriesName($categoryIds)
    {
        if ($categoryIds) {
            $data = [];
            if ($this->listCategories) {
                $listCategories = $this->listCategories;
                foreach ($categoryIds as $key => $categoryId) {
                    if (array_key_exists($categoryId, (array)$listCategories)) {
                        $data[] = $listCategories[$categoryId];
                        unset($categoryIds[$key]);
                    }
                }
            }
            if ($categoryIds) {
                $categories = $this->getCategoriesNameByIds($categoryIds);
                foreach ($categories as $category) {
                    $this->listCategories[$category['entity_id']] = $category['name'];
                    $data[] = $category['name'];
                }
            }

            return $data;
        }
        return '';
    }

    /**
     * Get attribute code name
     *
     * @return int
     */
    public function getAttributeCodeIdName()
    {
        if (!$this->attributeCodeIdName) {
            $this->attributeCodeIdName = $this->attribute->getIdByCode('catalog_category', 'name');
        }
        return $this->attributeCodeIdName;
    }

    /**
     * Get categories name by categories ID
     *
     * @param array $categoryIds
     * @return array
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getCategoriesNameByIds($categoryIds)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $attributeCodeIdName = $this->getAttributeCodeIdName();
        $id = $this->data->isMagentoEE() ? "row_id" : "entity_id";

        $query = $this->resource->getConnection()
            ->select()
            ->from(
                [
                    'e' => $this->resource->getTableName('catalog_category_entity')
                ],
                [
                    'e.entity_id', "IF(`categoryEntityCurrent`.`value`,
                    `categoryEntityCurrent`.`value`, `categoryEntity`.`value`) as name"
                ]
            )
            ->joinLeft(
                [
                    'categoryEntity' => $this->resource->getTableName('catalog_category_entity_varchar')
                ],
                sprintf(
                    'e.entity_id = categoryEntity.%s AND categoryEntity.store_id = 0',
                    $id
                ),
                $id
            )
            ->joinLeft(
                ['categoryEntityCurrent' => $this->resource->getTableName('catalog_category_entity_varchar')],
                sprintf(
                    'e.entity_id = categoryEntityCurrent.%s AND categoryEntityCurrent.store_id = %s ',
                    $id,
                    $storeId
                ),
                'attribute_id'
            )
            ->where(
                sprintf(
                    "e.entity_id in (%s)) AND (categoryEntity.attribute_id = %s",
                    implode(',', $categoryIds),
                    $attributeCodeIdName
                )
            );
        return $this->resource->getConnection()->query($query)->fetchAll();
    }

    /**
     * Get base info product
     *
     * @param Product $product
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getProductBaseInfo($product)
    {
        if (!$product->getCategoryIds() && !empty($product->getProduct())) {
            $itemCategoryId = array_slice($product->getProduct()->getCategoryIds(), 0, 5, true);
        } else {
            $itemCategoryId = array_slice($product->getCategoryIds(), 0, 5, true);
        }
        $itemCategory = $this->getCategoriesName($itemCategoryId);
        $qty = $product->getQty() > 0 ? $product->getQty() : 1;
        $price = $this->getPriceInfo($product);
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
        $finalPrice = $product->getPriceInfo()->getPrice('final_price')
            ->getMinimalPrice()->getValue();
        if ($product->getTypeId() == "grouped") {
            $price = $finalPrice = $product->getFinalPrice();
        }
        if ($product->getTierPrice()) {
            $price = $finalPrice = $product->getFinalPrice((int)$qty);
        }
        return [
            "itemCategory" => $itemCategory,
            "qty" => $qty,
            "price" => $price,
            "discount" => $regularPrice != 0 && $regularPrice != $finalPrice ? abs($regularPrice - $finalPrice) : 0
        ];
    }

    /**
     * Get Product model
     *
     * @param Item|\Magento\Sales\Model\Order\Item|Product $product
     * @return Product
     */
    public function getProductModel($product)
    {
        if ($product instanceof Item ||
            $product instanceof \Magento\Sales\Model\Order\Item ||
            $product instanceof \Magento\Quote\Model\Quote\Address\Item
        ) {
            return $product->getProduct();
        }
        return $product;
    }

    /**
     * Get attribute Option
     *
     * @param array $productOptions
     * @return string
     */
    public function getAttributeInfo($productOptions)
    {
        $variant = [];
        foreach ($productOptions as $productOption) {
            $variant[] = $productOption['label'];
        }
        return implode(',', $variant);
    }

    /**
     * Get Variant configurable product
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return string
     */
    public function getVariantConfigurable($item)
    {
        if ($item->getCustomOptions() && isset($item->getCustomOptions()['attributes'])) {
            $optionsSelected = $item->getCustomOptions()['attributes']->getValue();
            $allOption = $item->getTypeInstance()->getConfigurableAttributesAsArray($item);
            if ($optionsSelected) {
                $optionsSelected = $this->data->unSerializeItem($optionsSelected);
                $variant = [];
                foreach ($optionsSelected as $key => $optionValue) {
                    $option = $allOption[$key];
                    foreach ($allOption[$key]['values'] as $value) {
                        if ($value['value_index'] == $optionValue) {
                            $variant[] = $option['store_label'] . ': ' . $value['store_label'];
                        }
                    }
                }
                if ($variant) {
                    return implode(',', $variant);
                }
            }
        }
        return '';
    }

    /**
     * Get all data item events payment and shipping
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getItemsPaymentShipping()
    {
        $data = [];
        if ($items = $this->checkoutSession->getQuote()->getItems()) {
            foreach ($items as $key => $item) {
                $product = $this->renderItem($item, $key + 1);
                $product['price'] = $this->data->convertPriceCurrency((float)$item->getPrice());
                $product['quantity'] = $item->getQty();
                if ($item->getProductType() == "configurable") {
                    $childProduct = $item->getChildren()[0];
                    $product["item_id"] = $childProduct->getProductId();
                    $product['item_variant'] = $this->getVariantConfigurable($item->getProduct());
                }
                $data[] = $product;
            }
        }
        return $data;
    }

    /**
     * Get data item to event view cart and view checkout
     *
     * @param mixed $product
     * @param int $key
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getDataItemCheckout($product, $key)
    {
        $item = $this->renderItem($product, $key + 1);
        if ($product->getProductType() == "configurable") {
            $childProduct = $product->getChildren()[0];
            $item["item_id"] = $childProduct->getProductId();
            $item['item_variant'] = $this->getVariantConfigurable($product->getProduct());
        }
        $item["price"] = $this->data->convertPriceCurrency($product->getPrice());
        $item['quantity'] = $product->getQty();
        return $item;
    }
}
