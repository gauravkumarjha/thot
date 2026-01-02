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
namespace Lof\ShopByBrand\Block;

class ShopByBrandpage extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\ShopByBrand\Model\Items
     */
    protected $_shopbybrand;

    protected $_collection = null;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Lof\ShopByBrand\Helper\Data $shopbybrandHelper
     * @param \Ves\Brand\Helper\Data $brandHelper
     * @param \Ves\Brand\Model\Brand $brand
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\ShopByBrand\Helper\Data $shopbybrandHelper,
        \Ves\Brand\Helper\Data $brandHelper,
        \Ves\Brand\Model\Brand $brand,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_shopbybrandHelper = $shopbybrandHelper;
        $this->_brand = $brand;
        $this->_brandHelper = $brandHelper;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        if (!$this->getConfig('general_settings/enable')) {
            return;
        }
        parent::_construct();
        $template = '';
        $layout = $this->getConfig('brand_list_page/layout');
        $enable_alphabet_layout = $this->getShopbybrandConfig('general/enable_alphabet_layout');
        if ($enable_alphabet_layout) {
            $layout = "alphabet";
        }
        if ($layout == 'grid') {
            $template = 'Ves_Brand::brandlistpage_grid.phtml';
        } elseif ($layout == 'alphabet') {
            $template = 'Lof_ShopByBrand::brandlistpage_alphabet.phtml';
        } else {
            $template = 'Ves_Brand::brandlistpage_list.phtml';
        }
        if (!$this->hasData('template')) {
            $this->setTemplate($template);
        }
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = $this->_brandHelper->getConfig('brand_list_page/page_title');

        if ($breadcrumbsBlock) {

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $baseUrl
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'vesbrand',
                [
                'label' => $page_title,
                'title' => $page_title,
                'link' => ''
                ]
            );
        }
    }

    /**
     * Set brand collection
     * @param \Lof\ShopByBrand\Model\ResourceModel\Items\Collection
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * Retrive brand collection
     */
    public function getCollection()
    {
        if ($this->_collection == null) {
            $store = $this->_storeManager->getStore();
            $brand = $this->_brand;
            $brandCollection = $brand->getCollection()
            ->addFieldToFilter('status', 1)
            //->addStoreFilter($store)
            ->setOrder('position', 'ASC');

            $brandCollection->getSelect()->reset(\Zend_Db_Select::ORDER);
            $brandCollection->setOrder('position', 'ASC');
            $this->setCollection($brandCollection);
        }

        return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_brandHelper->getConfig($key);
        if (!$result) {

            return $default;
        }
        return $result;
    }

    public function getShopbybrandConfig($key, $default = '')
    {
        $result = $this->_shopbybrandHelper->getConfig($key);
        if (!$result) {

            return $default;
        }
        return $result;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page_title = $this->getConfig('brand_list_page/page_title');
        $meta_description = $this->getConfig('brand_list_page/meta_description');
        $meta_keywords = $this->getConfig('brand_list_page/meta_keywords');
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('ves-brandlist');
        if ($page_title) {
            $this->pageConfig->getTitle()->set($page_title);
        }
        if ($meta_keywords) {
            $this->pageConfig->setKeywords($meta_keywords);
        }
        if ($meta_description) {
            $this->pageConfig->setDescription($meta_description);
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('lofshopbybrand_toolbar');
        if ($block) {
            $block->setDefaultOrder("position");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        $toolbar = $this->getToolbarBlock();
        $pretext = $this->getShopbybrandConfig('brand_list_page/pretext');
        if ($pretext) {
            $pretext                   = $this->_brandHelper->filter($pretext);
        }
        $this->setData("pretext", $pretext);

        // set collection to toolbar and apply sort
        $itemsperpage = (int)$this->getConfig('brand_list_page/item_per_page', 0);
        if (!$itemsperpage) {
            $toolbar = false;
        }
        if ($toolbar) {
            $toolbar->setData('_current_limit', $itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        $layout = $this->getConfig('brand_list_page/layout');
        $enable_alphabet_layout = $this->getShopbybrandConfig('general/enable_alphabet_layout');
        if ($enable_alphabet_layout) {
            $layout = "alphabet";
        }
        if ($layout == 'alphabet') {
            $collection = $this->sortBrandByAlphabet($collection);
            $this->setCollection($collection);
        }

        return parent::_beforeToHtml();
    }

    public function getAlphabetLetters()
    {
        $alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        return $alphabet;
    }

    public function sortBrandByAlphabet($collection = null)
    {
        if (!$collection) {
            $collection = $this->getCollection();
        }
        $letters = $this->getAlphabetLetters();
        $output = [];
        foreach ($letters as $letter) {
            $output[$letter] = [];
        }
        $output["#"] = [];

        foreach ($collection as $_brand) {
            $brand_name = $_brand->getName();
            $letter = strtoupper(substr($brand_name, 0, 1));
            if (!in_array($letter, $letters)) {
                $letter = "#";
            }
            $output[ $letter ][] = $_brand; // Or, whatever you want to output.

        }
        return $output;
    }

    public function getBrandToolTip($_brand)
    {
        $thumbnail_url = $_brand->getThumbnailUrl();
        $description = $_brand->getDescription();
        $description = $this->_brandHelper->filter($description);
        $description = strip_tags($description, ["p","b"]);
        $html = $description;
        if ($thumbnail_url) {
            $html .= '<img class="lof-brand-image" src="'.$thumbnail_url.'"/>';
        }
        $html = str_replace(["\r\n", "\r", "\n"], '', $html);
        return str_replace(["<",">",'"'], ["&lt;","&gt;","&quot;"], $html);
    }
}
