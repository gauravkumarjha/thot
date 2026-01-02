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
 * @copyright  Copyright (c) 2022-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GA4\Block\Product;

use Bss\GA4\Model\Config;
use Bss\GA4\Model\DataItem;
use Bss\GA4\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Block\Product\ProductList\Upsell;
use Magento\Catalog\Model\CategoryRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\UrlRewrite\Model\UrlFinderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var null
     */
    protected $initProduct;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Related
     */
    protected $related;

    /**
     * @var Upsell
     */
    protected $upsell;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param UrlFinderInterface $urlFinder
     * @param CategoryRepository $categoryRepository
     * @param DataItem $additionalData
     * @param Config $config
     * @param Related $related
     * @param Upsell $upsell
     * @param StockRegistryInterface $stockRegistry
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Template\Context $context,
        \Bss\GA4\Helper\Data $dataHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Bss\GA4\Model\DataItem $additionalData,
        \Bss\GA4\Model\Config $config,
        \Magento\Catalog\Block\Product\ProductList\Related $related,
        \Magento\Catalog\Block\Product\ProductList\Upsell $upsell,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->dataHelper = $dataHelper;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->urlFinder = $urlFinder;
        $this->categoryRepository = $categoryRepository;
        $this->additionalData = $additionalData;
        $this->config = $config;
        $this->related = $related;
        $this->upsell = $upsell;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Get value
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getValue()
    {
        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->productRepository->getById($id, false, (string)$storeId);
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $minQty = $stockItem->getMinSaleQty();
        if ($product->getTypeId() == "bundle") {
            return $this->additionalData->getPriceInfo($product);
        }
        if ($product->getTypeId() == "simple") {
            return $this->dataHelper->convertPriceCurrency($product->getFinalPrice($minQty));
        }
        return $product->getFinalPrice();
    }

    /**
     * Get item
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getItem()
    {
        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        return $this->itemArray($id, 0, true);
    }

    /**
     * Serialize item
     *
     * @param array $item
     * @return bool|string
     */
    public function serializeItem($item)
    {
        return $this->dataHelper->serializeItem($item);
    }

    /**
     * Get upsell product
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getUpSellProduct()
    {
        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        $items = [];
        //upsell product
        $upSellProducts = $this->upsell->getItems();
        if ($upSellProducts) {
            $index = 1;
            foreach ($upSellProducts as $upSellProduct) {
                $item = $this->itemArray($upSellProduct->getId(), $index);
                $item['item_list_id'] = $id;
                $item['item_list_name'] = __("Up-Sell Products");
                $items['items'][] = $item;
                $items['item_list_id'] = $id;
                $items['item_list_name'] = __("Up-Sell Products");
                $index++;
            }
        }
        return $items;
    }

    /**
     * Get related product
     *
     * @return array|false
     * @throws NoSuchEntityException
     */
    public function getRelatedProduct()
    {
        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        $items = [];
        //Related product
        $relatedProductProducts = $this->related->getItems();
        if ($relatedProductProducts) {
            $index = 1;
            foreach ($relatedProductProducts as $item) {
                $item = $this->itemArray($item->getId(), $index);
                $item['item_list_id'] = $id;
                $item['item_list_name'] = __("Related Products");
                $items['items'][] = $item;
                $items['item_list_id'] = $id;
                $items['item_list_name'] = __("Related Products");
                $index++;
            }
        }
        return $items;
    }

    /**
     * Render Item array
     *
     * @param int $id
     * @param int $index
     * @param bool $flag
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function itemArray($id, $index = 0, $flag = false)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->productRepository->getById($id, false, (string)$storeId);
        if (!$flag) {
            $this->additionalData->setItemVariant = false;
        }
        $item = $this->additionalData->renderItem($product, $index);
        if ($flag) {
            if ($product->getTypeId() == "simple") {
                $stockItem = $this->stockRegistry->getStockItem($product->getId());
                $minQty = $stockItem->getMinSaleQty();
                $item['price'] =  $this->dataHelper->convertPriceCurrency($product->getFinalPrice($minQty));
            }
            $item['item_list_name'] = $product->getName();
        }
        $productQty = $product->getExtensionAttributes()->getStockItem();
        if (isset($productQty) && $productQty->getMinSaleQty() > 1) {
            $item['quantity'] = $productQty->getMinSaleQty();
        }
        return $item;
    }

    /**
     * Get previous Url
     *
     * @return array|string
     * @throws NoSuchEntityException
     */
    public function getPreviousUrl()
    {
        $baseUrl = $this->_urlBuilder->getBaseUrl();
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if ($refererUrl) {
            $filterData = [
                \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::REQUEST_PATH => str_replace($baseUrl, '', $refererUrl)
            ];
            $rewrite = $this->urlFinder->findOneByData($filterData);
            if ($rewrite) {
                $entityId = $rewrite->getEntityId();
                if ($rewrite->getEntityType() != "product") {
                    $category = $this->categoryRepository
                        ->get($entityId, $this->_storeManager->getStore()->getId());
                    return ['item_list_id' => $entityId, 'item_list_name' => $category->getName()];
                } else {
                    $requestUri = $this->getRequest()->getServer('REQUEST_URI');
                    if (strpos($refererUrl, $requestUri) !== false) {
                        return '';
                    }
                    $product = $this->productRepository->getById($entityId);
                    return ['item_list_id' => $entityId, 'item_list_name' => $product->getName()];
                }
            }
            return ['item_list_id' => '', 'item_list_name' => "Home Page"];
        }
        return '';
    }

    /**
     * Get currency
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrency()
    {
        return $this->dataHelper->getCurrency();
    }

    /**
     * Is enable module
     *
     * @return mixed
     */
    public function isEnableModule()
    {
        return $this->config->enableModule();
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }
}
