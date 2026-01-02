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
namespace Bss\GA4\Block\Category;

use Bss\GA4\Helper\Data;
use Bss\GA4\Model\Config;
use Bss\GA4\Model\DataItem;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class ListProduct extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
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
     * @var Layer
     */
    protected $catalogLayer;

    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param Context $context
     * @param Resolver $layerResolver
     * @param DataItem $additionalData
     * @param Config $config
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Resolver $layerResolver,
        \Bss\GA4\Model\DataItem $additionalData,
        \Bss\GA4\Model\Config $config,
        \Bss\GA4\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->layerResolver = $layerResolver;
        $this->catalogLayer = $layerResolver->get();
        $this->additionalData = $additionalData;
        $this->config = $config;
        $this->dataHelper = $dataHelper;
        $this->data = $data;
        parent::__construct($context, $data);
    }

    /**
     * Get current category
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->catalogLayer->getCurrentCategory();
    }

    /**
     * Get view list
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getViewItemList()
    {
        $productCollection = $this->catalogLayer->getProductCollection();
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
                $item["item_list_id"] = $this->catalogLayer->getCurrentCategory()->getId();
                $item["item_list_name"] = $this->getItemListName();
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
    public function getItemListName()
    {
        if ($this->getRequest()->getFullActionName() == "catalogsearch_result_index") {
            if ($this->getLayout()->getBlock('page.main.title')) {
                return $this->getLayout()->getBlock('page.main.title')
                    ->getPageTitle()->getText();
            }
            if ($this->getLayout()->getBlock('search.title')) {
                return $this->getLayout()->getBlock('search.title')
                    ->getPageTitle()->getText();
            }
            return '';
        }
        return $this->catalogLayer->getCurrentCategory()->getName();
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
        return $this->_escaper;
    }
}
