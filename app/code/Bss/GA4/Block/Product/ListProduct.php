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
namespace Bss\GA4\Block\Product;

use Bss\GA4\Model\Config;
use Bss\GA4\Model\DataItem;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;

class ListProduct implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Bss\GA4\Helper\Data
     */
    protected $dataHelper;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Current category. (Set current category)
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $currentCategory;

    /**
     * Full action name request. (Set full action name)
     *
     * @var string
     */
    protected $fullActionName = '';

    /**
     * Layout. (Set layout)
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * Construct.
     *
     * @param DataItem $additionalData
     * @param SerializerInterface $serializer
     * @param Config $config
     * @param \Bss\GA4\Helper\Data $dataHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Model\Session $catalogSession
     */
    public function __construct(
        \Bss\GA4\Model\DataItem $additionalData,
        SerializerInterface $serializer,
        \Bss\GA4\Model\Config $config,
        \Bss\GA4\Helper\Data $dataHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Session $catalogSession
    ) {
        $this->additionalData = $additionalData;
        $this->serializer = $serializer;
        $this->config = $config;
        $this->dataHelper = $dataHelper;
        $this->escaper = $escaper;
        $this->catalogSession = $catalogSession;
    }

    /**
     * Get current category.
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory($category = null)
    {
        if ($category) {
            $this->currentCategory = $category;
        }

        return $this->currentCategory;
    }

    /**
     * Get view list
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getViewItemList($productCollection)
    {
        if ($productCollection->count()) {
            $items = [];
            $index = 1;
            $this->additionalData->setItemVariant = false;
            foreach ($productCollection as $product) {
                $item = $this->additionalData->renderItem($product, $index);
                if ($product->getTypeId() == "simple") {
                    //default qty in list item set is 1
                    $item["price"] = $this->dataHelper->convertPriceCurrency($product->getFinalPrice(1));
                }
                $item["item_list_id"] = $this->getCurrentCategory()->getId();
                $item["item_list_name"] = $this->getItemListName($this->fullActionName, $this->layout);
                $items[] = $item;
                $index++;
            }
            return array_chunk($items, 50);
        }
        return [];
    }

    /**
     * Get item list name
     *
     * @return string
     * @throws LocalizedException
     */
    public function getItemListName($fullActionName, $layout)
    {
        /* Set action name and layout. */
        $this->fullActionName = $fullActionName;
        $this->layout = $layout;

        if ($fullActionName === "catalogsearch_result_index") {
            return $layout->getBlock('page.main.title')->getPageTitle()->getText();
        }

        return $this->getCurrentCategory()->getName();
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
     * Serialize item
     *
     * @param array $data
     * @return bool|string
     */
    public function serializeItem($data)
    {
        return $this->dataHelper->serializeItem($data);
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->escaper;
    }

    /**
     * Get CatalogSession
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        return $this->catalogSession;
    }

    /**
     * Get gtag when ajax complete
     *
     * @param array $dataItemsList
     * @param int|string $categoryId
     * @param string $itemListName
     *
     * @return array
     */
    public function getGtagAjaxLayer($dataItemsList, $categoryId, $itemListName)
    {
        $gtag = [];
        if (isset($dataItemsList) && isset($categoryId) && isset($itemListName)) {
            foreach ($dataItemsList as $listItem) {
                $gtag[] = '[
                    "event", "view_item_list", {
                        "item_list_id" : "'. $categoryId .'",
                        "item_list_name" : "'. $itemListName .'",
                        "items": '. $this->serializeItem($listItem) .'
                    }
                ]';
            }
        }
        return $gtag;
    }
}
