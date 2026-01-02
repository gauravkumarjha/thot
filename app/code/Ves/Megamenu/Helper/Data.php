<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Megamenu\Helper;

use Magento\Widget\Model\Template\FilterEmulate;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var array
	 */
	static $arr = [];

	/**
	 * @var array
	 */
	static $categories = [];

    /**
     * @var string
     */
	static $_hidden_menu_content_1 = "hidden-xs hidden-sm";

    /**
     * @var string
     */
	static $_hidden_menu_content_2 = "hidden-lg hidden-md";

	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_filterProvider;

	/**
     * @var \Magento\Cms\Model\Template\Filter
     */
	protected $_filter;

	/**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
	protected $_categoryFactory;

    /**
     * @var array|mixed|null
     */
	protected $menu;

	/**
     * @var \Magento\Framework\Escaper
     */
	protected $_escaper;

    /**
     * @var array|mixed|null
     */
	protected $_cats;

	/**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;

    /**
     * @var \Magento\Framework\Url
     */
	protected $_url;

	/**
     * Group Collection
     * @var array|mixed|null
     */
	protected $_groupCollection;

    /**
     * @var array|mixed|null
     */
	protected $_currentStore;

    /**
     * @var string
     */
	protected $mediaUrl;

    /**
     * @var string
     */
	protected $baseUrl;

    /**
     * @var array|mixed|null
     */
	protected $_catsCollection;

    /**
     * @var array|mixed|null
     */
	protected $menuCategories = [];

    /**
     * @var array|mixed
     */
	protected $_category_list = [];

    /**
     * @var array|mixed
     */
	protected $_cached_category_links = [];

    /**
     * @var array|mixed
     */
	protected $countParentLevel = [];

	/**
     * @var \Ves\Megamenu\Helper\MobileDetect
     */
    protected $_mobileDetect;

    /**
     * @var string|int|null
     */
    protected $_currentStoreId = null;

    /**
     * @var string
     */
    protected $_submenu_sort_type = "alphabet";//normal | alphabet

    /**
     * @var int
     */
    protected $_submenu_isgroup_level = 1;//number level of sub menu items which enable isgroup

    /**
     * @var FilterEmulate
     */
    private $widgetFilter;

    /**
     * @var State
     */
    private $state;

    /**
     * @var bool
     */
    protected $allowChangeState = false;

	/**
     * Construct megamenu helper data
     *
	 * @param \Magento\Framework\App\Helper\Context      $context
	 * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
	 * @param \Magento\Cms\Model\Template\Filter         $filter
	 * @param \Magento\Framework\Registry                $registry
	 * @param \Magento\Framework\Escaper                 $escaper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Catalog\Model\CategoryFactory     $categoryFactory
	 * @param \Magento\Customer\Model\Group              $groupManager
	 * @param \Magento\Framework\Url                     $url
	 * @param \Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories
	 * @param \Ves\Megamenu\Helper\MobileDetect                $mobileDetectHelper
     * @param FilterEmulate $widgetFilter
     * @param State $state
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Cms\Model\Template\Filter $filter,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Escaper $escaper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Customer\Model\Group $groupManager,
		\Magento\Framework\Url $url,
		\Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories,
		\Ves\Megamenu\Helper\MobileDetect $mobileDetectHelper,
        FilterEmulate $widgetFilter,
        State $state
	) {
		parent::__construct($context);
		$this->_filterProvider  = $filterProvider;
		$this->_filter          = $filter;
		$this->_coreRegistry    = $registry;
		$this->_categoryFactory = $categoryFactory;
		$this->_escaper         = $escaper;
		$this->_storeManager    = $storeManager;
		$this->_url             = $url;
		$this->_groupCollection = $groupManager;
		$this->mediaUrl         = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$this->baseUrl         = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_DIRECT_LINK);
		$this->storeCategories  = $storeCategories;
		$this->_mobileDetect    = $mobileDetectHelper;
        $this->widgetFilter = $widgetFilter;
        $this->state = $state;
	}

    /**
     * Set store
     *
     * @param mixed|object $store
     * @return $this
     */
	public function setStore($store) {
		$this->_currentStore = $store;
		return $this;
	}

    /**
     * get store
     *
     * @return mixed|object $store
     */
	public function getStore() {
		return $this->_currentStore;
	}

    /**
     * Filter data
     *
     * @param string $str
     * @param int|string|mixed $storeId
     * @return string
     */
	public function filter($str, $storeId = '')
	{
		if ($str) {
			$filter = $this->_filterProvider->getPageFilter();
			if ($storeId && $storeId != 'all') {
				$storeId = (int)$storeId;
				$filter->setStoreId($storeId);
			}
            try {
			    $html   = $filter->filter($str);
            } catch (\Exception $e) {
                $html = $str;
            }
			return $html;
		}
		return $str;
	}

    /**
     * Set allow change state
     *
     * @param bool $flag
     * @return $this
     */
    public function setAllowChangeState($flag = false)
    {
        $this->allowChangeState = $flag;
        return $this;
    }

    /**
     * Filter Widget data
     *
     * @param string $content
     * @return string
     */
	public function filterWidget($content)
	{
		if ($content) {
			$renderedContent = $this->widgetFilter->filter($content);
			return $renderedContent;
		}
		return $content;
	}

    /**
     * Process import from file
     *
     * @return void
     */
    public function processFilterWidget($content)
    {
        $allowChangeState = $this->allowChangeState;
        if ($allowChangeState) {
            return $this->state->emulateAreaCode(
                Area::AREA_GRAPHQL,
                [$this, "filterWidget"],
                [$content, "content" => $content]
            );
        } else {
            return $this->filterWidget($content);
        }
    }

    /**
     * Sub string
     *
     * @param string $text
     * @param int $length
     * @param string $replacer
     * @param bool $is_striped
     * @return string
     */
	public function subString($text, $length, $replacer = '...', $is_striped = true)
    {
		if ((int)$length==0) return $text;
		$text = ($is_striped == true) ? strip_tags($text) : $text;
		if (strlen($text) <= $length) {
			return $text;
		}
		$text = substr($text, 0, $length);
		$pos_space = strrpos($text, ' ');
		return substr($text, 0, $pos_space) . $replacer;
	}

    /**
     * Get customer groups
     *
     * @return array|mixed
     */
	public function getCustomerGroups()
	{
		$data_array = [];

		$customer_groups = $this->_groupCollection->getCollection();

		foreach ($customer_groups as $item_group) {
			$data_array[] =  array('value' => $item_group->getId(), 'label' => $item_group->getCode());
		}

		return $data_array;

	}

    /**
     * Decode widgets
     *
     * @param string $str
     * @param int|mixed|string $storeId
     * @param bool $checkFilter
     * @return string
     */
	public function decodeWidgets($str, $storeId = '', $checkFilter = true)
    {
        if (empty($str)) return $str;
		$result = '';
		$imgs = [];
		$firstPosition = 0;
		$i = 0;
		$count = substr_count($str, 'title="{{widget');
		for ($i=0; $i < $count; $i++) {
			if ($firstPosition==0) $tmp = $firstPosition;
			$firstPosition = strpos($str, "<img", $tmp);
			$nextPosition = strpos($str, "/>", $firstPosition);
			$tmp = $firstPosition;
			$length = $nextPosition - $firstPosition;
			$img = substr($str, $firstPosition, $length+2);
			if (strpos($img, '{{widget')) {
				$f = strpos($img, "{{widget", 0);
				$n = strpos($img, '"', $f);
				$widgetCode = substr($img, $f, ($n-$f));
				$widgetHtml = $this->filter(html_entity_decode($widgetCode), $storeId);
				if ($i==0) $result = $str;
				$result = str_replace($img, $widgetHtml, $result);
				$str = str_replace($img, '', $str);
			}
		}

		$count = substr_count($str, 'title="{widget');
		for ($i=0; $i < $count; $i++) {
			if ($firstPosition==0) $tmp = $firstPosition;
			$firstPosition = strpos($str, "<img", $tmp);
			$nextPosition = strpos($str, "/>", $firstPosition);
			$tmp = $firstPosition;
			$length = $nextPosition - $firstPosition;
			$img = substr($str, $firstPosition, $length+2);
			if (strpos($img, '{widget')) {
				$f = strpos($img, "{widget", 0);
				$n = strpos($img, '"', $f);
				$widgetCode = '{' . substr($img, $f, ($n-$f)) . '}';
				$widgetHtml = $this->filter(html_entity_decode($widgetCode), $storeId);
				if ($i==0) $result = $str;
				$result = str_replace($img, $widgetHtml, $result);
				$str = str_replace($img, '', $str);
			}
		}

		$widgets = ['Magento_Widget/placeholder.gif', 'Magento_Catalog/images/product_widget_new.png', 'Magento_CatalogWidget/images/products_list.png', 'Magento/backend/en_US/Magento_Reports/images/product_widget_viewed.gif'];

		for ($z=0; $z < count($widgets); $z++) {
			$count = substr_count($str, $widgets[$z]);
			for ($i=0; $i < $count; $i++) {
				if ($firstPosition==0) $tmp = $firstPosition;
				$firstPosition = strpos($str, "<img", $tmp);
				$nextPosition = strpos($str, "/>", $firstPosition);
				$tmp = $firstPosition;
				$length = $nextPosition - $firstPosition;
				$img = substr($str, $firstPosition, $length+2);
				if (strpos($img, 'id="')) {
					$f = strpos($img, 'id="', 0);
					$n = strpos($img, '"', $f+4);
					$widgetCode = substr($img, $f+4, ($n-$f-4));
					$widgetCode = str_replace("--", "", $widgetCode);
					$widgetCode = base64_decode($widgetCode);
					$widgetHtml = $widgetCode;
					if ($i==0) $result = $str;
					$result = str_replace($img, $widgetHtml, $result);
					$str .= str_replace($img, '', $str);
				}
			}
		}

		if ($result != '' && !empty($result)) {
			return $result;
		} elseif(!empty($str) && $checkFilter) {
            return $this->filter($str);
        }
		return $str;
	}

	/**
     * Return megamenu config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
	public function getConfig($key, $store = null)
	{
        $store = $this->_storeManager->getStore($store);
        //$websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'vesmegamenu/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
	}

    /**
     * get option disable menu title attribute
     * @param \Magento\Store\Model\Store|int|string $store
     * @return bool
     */
    public function isDisableMenuTitle($store = null)
    {
        return (bool) $this->getConfig("general_settings/disable_menu_title");
    }

    /**
     * Get core registry
     *
     * @return \Magento\Framework\Registry
     */
	public function getCoreRegistry()
	{
		return $this->_coreRegistry;
	}

    /**
     * Render menu item data
     *
     * @param array|mixed|object $data
     * @param array|mixed $itemBuild
     * @param array|mixed $menuItems
     * @return mixed|array
     */
	public function renderMenuItemData($data = [] , $itemBuild = [], $menuItems = [])
	{
		$data_id = isset($data['id'])?$data['id']:0;
		$itemBuild = isset($menuItems[$data_id])?$menuItems[$data_id]:[];
		$children = [];
		if (isset($data['children']) && (count($data['children'])>0)) {
			foreach ($data['children'] as $k => $v) {
				$children[] = $this->renderMenuItemData($v, $itemBuild, $menuItems);
			}
		}
		$itemBuild['children'] = $children;
		return $itemBuild;
	}

    /**
     * Get menu profile
     *
     * @return mixed|object|array|null
     */
	public function getMenu()
	{
		return $this->menu;
	}

    /**
     * get current store Id
     * @return string|int|null
     */
    public function getCurrentStoreId()
    {
        return $this->_currentStoreId;
    }

    /**
     * get current store Id
     * @param string|int|null $storeId = null
     * @return $this
     */
    public function setCurrentStoreId($storeId = null)
    {
        $this->_currentStoreId = $storeId;
        return $this;
    }

    /**
     * get current store
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStore()
    {
        $currentStoreId = $this->getCurrentStoreId();
        return $this->_storeManager->getStore($currentStoreId);
    }

    /**
     * Draw anchor
     *
     * @param mixed|object|array $item
     * @param int $level
     * @param bool $return_link
     * @return mixed|object|array|string
     */
	public function drawAnchor($item, $level = 1, $return_link = false)
	{
		$hasChildren = false;
		$tChildren = false;
		if($this->isMobileDevice() && $this->isCheckMobileDevice()) {
			if($item['content_type'] == 'dynamic'){
				$item['content_type'] = 'childmenu';
			}
		}
		if ($item['content_type'] == 'parentcat') {
			$catChildren = $this->getTreeCategories($item['parentcat']);
			if ($catChildren) $tChildren = true;
		}
		if (($item['show_footer'] && $item['footer_html']!='') || ($item['show_header'] && $item['header_html']!='') ||  ($item['show_left_sidebar'] && $item['left_sidebar_html']!='') || ($item['show_right_sidebar'] && $item['right_sidebar_html']!='') || ($item['show_content'] && ((($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') && (isset($item['children']) && count($item['children'])>0)) || ($item['content_type'] == 'content' && $item['content_html']!=''))) || ($item['content_type'] == 'parentcat' && $tChildren) ) $hasChildren = true;

		$html = $class = $style = $attr = '';

		// Design
		if (isset($item['color']) && $item['color']!='') {
			$style .= 'color: ' . $item['color'] . ';';
		}
		if (isset($item['bg_color']) && $item['bg_color']!='') {
			$style .= 'background-color: ' . $item['bg_color'] . ';';
		}
		if (isset($item['inline_css']) && $item['inline_css']!='') {
			$style .= $item['inline_css'];
		}
		if ($style!='') $style = 'style="' . $style . '"';

		$class .= ' nav-anchor';

		if ($item['content_type'] == 'dynamic') $class .= ' subdynamic';
		if ($item['is_group']) $class .= ' subitems-group';

		// Custom Link, Category Link
		$href = '';
		if ($item['link_type'] == 'custom_link') {
			$href = $this->filter($item['link']);

			if ($href !== null && $this->endsWith($href, '/')) {
				$href = substr_replace($href, "", -1);
			}
		} else if ($item['link_type'] == 'category_link') {
			if ($category = $this->getCategory($item['category'])) {
				$href = $category['url'];
				if($urls = parse_url($href)){
					$url_host = isset($urls['host'])?$urls['host']:"";
					$base_url = $this->_storeManager->getStore()->getBaseUrl();
					if($url_host && ($base_urls = parse_url($base_url))) {
						$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
						if($url_host != $base_urls['host']){
							$href = str_replace($url_host, $base_urls['host'], $href);
						}
					}
				}
			}
		}

		if ($return_link) { //If return link item, will return item, else return html code
			$item['href'] = $href;
			return $item;
		}

		if ($class!='') $class = 'class="' . $class . '"';

		// Attributes
		if (isset($item['hover_color']) && $item['hover_color']) {
			$attr .= ' data-hover-color="' . $item['hover_color'] . '"';
		}
		if (isset($item['bg_hover_color']) && $item['bg_hover_color']) {
			$attr .= ' data-hover-bgcolor="' . $item['bg_hover_color'] . '"';
		}

		if (isset($item['color']) && $item['color']) {
			$attr .= ' data-color="' . $item['color'] . '"';
		}

		if (isset($item['bg_color']) && $item['bg_color']) {
			$attr .= ' data-bgcolor="' . $item['bg_color'] . '"';
		}

        $disableMenuTitle = $this->isDisableMenuTitle();

		$target = $item['target']?'target="' . $item['target'] . '"':'';
		if ($href=='') $href = '#';
		if ($href == '#') $target = '';

		if ($item['name']!='' || $item['icon']) {
			$html .= '<a href="' . $href . '" '. ( !$disableMenuTitle ? ('title="' . strip_tags($item['name']) . '" ') : '' ) . $target . ' ' . $attr . ' ' . $style . ' ' . $class . '>';
		}

		if ($item['show_icon'] && $item['icon_classes']!='') {
			$html .= '<i class="' .$item['icon_classes'] . '"></i>';
		}

		// Icon Left
		if ($item['show_icon'] && $item['icon_position']=='left' && $item['icon']!='') {
			$html .= '<img class="item-icon icon-left" ';
			if ($item['hover_icon']) $html .= ' data-hoverimg="' . $item['hover_icon'] . '"';
			$html .= ' src="' . $item['icon'] . '" alt="' . strip_tags($item['name']) . '"/>';
		}

		if ($item['name']!='') {
			$html .= '<span>' . $item['name'] . '</span>';
		}

		// Icon Right
		if ($item['show_icon'] && $item['icon_position']=='right' && $item['icon']!='') {
			$html .= '<img class="item-icon icon-right" ';
			if ($item['hover_icon']) $html .= ' data-hoverimg="' . $item['hover_icon'] . '"';
			$html .= ' src="' . $item['icon'] . '" alt="' . strip_tags($item['name']) . '"/>';
		}

		if (isset($item['caret']) && $item['caret']) {
			$html .= '<i class="ves-caret fa ' . $item['caret'] . '"></i>';
		}

		if ($hasChildren) $html .= '<span class="opener"></span>';

		//if ($hasChildren) $html .= '<span class="drill-opener"></span>';

		if ($item['name']!='') {
			$html .= '</a>';
		}
		return $html;
	}

    /**
     * Get image link
     *
     * @param string $image_code
     * @return string
     */
	public function getImageLink($image_code) {
		$image_link = $image_code;
		$parsed = parse_url($image_code);
        if (!empty($parsed['scheme'])) {
            return $image_code;
        }
        if(strpos($image_code,"{{") && strpos($image_code,"}}")){
        	$image_link = $this->filter($image_code);
			$image_link = @trim($image_code);
        }

		return $image_link;
	}

    /**
     * Get config mobile menu
     *
     * @return int|bool|string
     */
	public function getConfigMobileMenu()
    {
		if (!isset($this->_is_use_mobile_menu)) {
			$this->_is_use_mobile_menu = $this->getConfig("general_settings/enable_mobile_menu");
		}
		return $this->_is_use_mobile_menu;
	}

    /**
     * Draw item for tree
     *
     * @param mixed|object|array $item
     * @param int $level
     * @param int $x
     * @param bool $listTag
     * @param int $parentIndex
     * @param bool $disable_mobile
     * @return mixed|object|array
     */
	public function drawItemForTree($item, $level = 0, $x = 1, $listTag = true, $parentIndex = 1, $disable_mobile = true)
    {
		$return_item = $item;
		$hasChildren = false;

		if (!isset($item['status']) || !$item['status']) {
			return;
		}

		if (isset($item['children']) && count($item['children'])>0) $hasChildren = true;
		// Custom Link, Category Link
		$href = '';
		if ($item['link_type'] == 'custom_link') {
			$href = $item['link'];
		} else if ($item['link_type'] == 'category_link') {
			if ($category = $this->getCategory($item['category'])) {
				$href = $category['url'];
				if($urls = parse_url($href)){
					$url_host = isset($urls['host'])?$urls['host']:"";
					$base_url = $this->_storeManager->getStore()->getBaseUrl();
					if($url_host && ($base_urls = parse_url($base_url))) {
						$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
						if($url_host != $base_urls['host']){
							$href = str_replace($url_host, $base_urls['host'], $href);
						}
					}
				}
			}
		}

		$link = $this->filter($href);
		$link = @trim($link);
		if ($this->endsWith($link, '/')) {
			$link = substr_replace($link, "", -1);
		}
        $item['link'] = $link;
		$item['href'] = $link;
		//$item['header_html'] =  $this->processFilterWidget($item['header_html']);
		//$item['left_sidebar_html'] =  $this->processFilterWidget($item['left_sidebar_html']);
		//$item['right_sidebar_html'] =  $this->processFilterWidget($item['right_sidebar_html']);
		//$item['footer_html'] =  $this->processFilterWidget($item['footer_html']);

		$sort_type = isset($item['submenu_sorttype'])?$item['submenu_sorttype']:'normal';

		// Content HTML
		if ($item['content_type'] == 'content' && $item['content_html'] !='' && $item['content_html']) {
			$item['content_html'] = $this->processFilterWidget($item['content_html']);
		}

		// Dynamic Tab
		if ($item['content_type'] == 'dynamic' && $hasChildren) {
			$children = $this->sortMenuItems($item['children'], $sort_type);
			$i = $z = 0;
			$child_tree = [];
			foreach ($children as $it) {
				if ($z==0) {
					$it['class'] = 'dynamic-active';
				}
				$it['dynamic'] = true;
				$child_tree[] = $this->drawItemForTree($it, $level, $i, false, $parentIndex, $disable_mobile);
				$i++;
				$z++;
			}
			$item['children'] = $child_tree;
		}

		// Child item
		if (($item['content_type'] == 'childmenu' && $hasChildren) || $item['content_type'] == 'parentcat') {
			if ($item['content_type'] == 'parentcat') {
				$isgroup_level = isset($item['isgroup_level'])?(int)$item['isgroup_level']:0;
				$cat_level = 1;
				$list = [];
				$max_level = 100;
				$catChildren = $this->getTreeCategories($item['parentcat'],$cat_level,$list,$max_level,$isgroup_level, $sort_type);
				$children = $this->sortMenuItems($catChildren, $sort_type);
			} else{
				$children = $this->sortMenuItems($item['children'], $sort_type);
			}
			$i = 0;
			$child_tree = [];
			foreach ($children as  $z => $it) {
				$it['htmlId'] = isset($it['htmlId'])?($it['htmlId'].'2'):(time().rand());
				$child_tree[] = $this->drawItemForTree($it, $level, $i, false, $parentIndex, false);
				$i++;
			}
			$item['children'] = $child_tree;
		}

		// Filter html content
        $item['header_html'] = isset($item['header_html']) ? $item['header_html'] : "";
        $item['left_sidebar_html'] = isset($item['left_sidebar_html']) ? $item['left_sidebar_html'] : "";
        $item['right_sidebar_html'] = isset($item['right_sidebar_html']) ? $item['right_sidebar_html'] : "";
        $item['footer_html'] = isset($item['footer_html']) ? $item['footer_html'] : "";
        $item['before_html'] = isset($item['before_html']) ? $item['before_html'] : "";
        $item['after_html'] = isset($item['after_html']) ? $item['after_html'] : "";

        if (isset($item['show_header'] ) && $item['show_header'] && $item['header_html']) {
		    $item['header_html'] = $this->processFilterWidget($item['header_html']);
        }
        if (isset($item['show_left_sidebar'] ) && $item['show_left_sidebar'] && $item['left_sidebar_html']) {
		    $item['left_sidebar_html'] = $this->processFilterWidget($item['left_sidebar_html']);
        }
        if (isset($item['show_right_sidebar'] ) && $item['show_right_sidebar'] && $item['right_sidebar_html']) {
		    $item['right_sidebar_html'] = $this->processFilterWidget($item['right_sidebar_html']);
        }
		if (isset($item['show_footer'] ) && $item['show_footer'] && $item['footer_html']) {
		    $item['footer_html'] = $this->processFilterWidget($item['footer_html']);
        }
        if ($item['before_html']) {
		    $item['before_html'] = $this->processFilterWidget($item['before_html']);
        }
        if ($item['after_html']) {
		    $item['after_html'] = $this->processFilterWidget($item['after_html']);
        }

		if(isset($item['dropdown_bgimage']) && !empty($item['dropdown_bgimage'])) {
			$item['dropdown_bgimage'] = $this->getImageLink($item['dropdown_bgimage']);
		}

		$return_item = $item;
		return $return_item;
	}

    /**
     * Draw item
     *
     * @param mixed|objct|array $item
     * @param int $level
     * @param int $x
     * @param bool $listTag
     * @param int $parentIndex
     * @param bool $disable_mobile
     * @return string
     */
	public function drawItem($item, $level = 0, $x = 1, $listTag = true, $parentIndex = 1, $disable_mobile = true)
	{
		$generate_mobile_menu_code = $this->getConfigMobileMenu();
		$html = "";
		try{
			$mediaUrl = $this->mediaUrl;
			$hasChildren = false;
			$class = $style = $attr = '';
			if (isset($item['class'])) $class = $item['class'];
			if (!isset($item['status']) || !$item['status']) {
				return;
			}
			if($this->isMobileDevice() && $this->isCheckMobileDevice()) {
				if($item['content_type'] == 'dynamic'){
					$item['content_type'] = 'childmenu';
				}
			}
			if (isset($item['children']) && count($item['children'])>0) $hasChildren = true;

			$nav_class = "";
			if($disable_mobile && ($item['link_type'] == 'category_link')){
				if($level === 0 && $parentIndex == 1){
					if(!isset($this->countParentLevel[$level])){
						$this->countParentLevel[$level] = 1;
					}else {
						$this->countParentLevel[$level] += 1;
					}
					$parentIndex = $this->countParentLevel[$level];
				}
				$nav_class = ' nav-' . $parentIndex;
			}
			$class .= ' nav-item level' . $level . $nav_class;
			// Item Align Type
			if ($item['align'] == '1') {
				$class .= ' submenu-left';
			} else if ($item['align'] == '2') {
				$class .= ' submenu-right';
			} else if ($item['align'] == '3') {
				$class .= ' submenu-alignleft';
			} else if ($item['align'] == '4') {
				$class .= ' submenu-alignright';
			}

			// Group Childs Item
			if ($item['is_group']) {
				$class .= ' subgroup ';
			} else {
				$class .= ' subhover ';
			}

			if ($item['content_type'] == 'dynamic') $class .= ' subdynamic';

			// Disable Dimesion
			if (((int)$item['disable_bellow'])>0)
				$attr .= 'data-disable-bellow="' . $item['disable_bellow'] . '"';

			if ($level==0) {
				$class .=' dropdown level-top';
			} else {
				$class .=' dropdown-submenu';
			}
			$class .= ' ' . $item['classes'];

			// Custom Link, Category Link
			$href = '';
			if ($item['link_type'] == 'custom_link') {
				$href = $item['link'];
			} else if ($item['link_type'] == 'category_link') {
				if ($category = $this->getCategory($item['category'])) {
					if($disable_mobile){
						$class .=' category-item';
					}
					$href = $category['url'];
					if($urls = parse_url($href)){
						$url_host = isset($urls['host'])?$urls['host']:"";
						$base_url = $this->_storeManager->getStore()->getBaseUrl();
						if($url_host && ($base_urls = parse_url($base_url))) {
							$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
							if($url_host != $base_urls['host']){
								$href = str_replace($url_host, $base_urls['host'], $href);
							}
						}
					}
				}
			}

			$link = $this->filter($href);
			$link = @trim($link);
			if ($this->endsWith($link, '/')) {
				$link = substr_replace($link, "", -1);
			}

			if ($hasChildren) {
				$class .= ' parent';
			}

			if ($item['show_icon'] && $item['icon_position']=='left' && $item['icon']!='') {
				$attr .= ' data-hovericon="' . $item['hover_icon'] . '" data-iconsrc="' . $item['icon'] . '"';
			}

			if (isset($item['caret']) && $item['caret']) {
				$attr .= ' data-hovercaret="' . $item['hover_caret'] . '" data-caret="' . $item['caret'] . '"';
			}

			if (isset($item['animation_in']) && $item['animation_in']) {
				$attr .= ' data-animation-in="' . $item['animation_in'] . '"';
			}

			if (isset($item['color']) && $item['color']) {
				$attr .= ' data-color="' . $item['color'] . '"';
			}

			if (isset($item['hover_color']) && $item['hover_color']) {
				$attr .= ' data-hover-color="' . $item['hover_color'] . '"';
			}

			if (isset($item['bg_color']) && $item['bg_color']) {
				$attr .= ' data-bgcolor="' . $item['bg_color'] . '"';
			}

			if (isset($item['bg_hover_color']) && $item['bg_hover_color']) {
				$attr .= ' data-hover-bgcolor="' . $item['bg_hover_color'] . '"';
			}


			if (!isset($item['htmlId'])) {
				$item['htmlId'] = time() . rand();
			}

			if ($listTag) {
				if ($class!='') $class = 'class="' . $class . '"';
				$html = '<li id="' . $item['htmlId'] . '" ' . $class . ' ' . $style . ' ' . $attr . '>';
			} else {
				if (isset($item['dynamic'])) {
					$class .= ' dynamic-item ' . $item['htmlId'];
				}
				if ($class!='') $class = 'class="' . $class . '"';
				$html = '<div id="' . $item['htmlId'] . '" ' . $class . ' ' . $style . ' ' . $attr . '>';
			}

			if (isset($item['before_html']) && $item['before_html']) {
				$html .= '<div class="item-before-content">' . $item['before_html'] . '</div>';
			}

			if (!isset($item['dynamic'])) $html .= $this->drawAnchor($item);
			$tChildren = false;
			if ($item['content_type'] == 'parentcat') {
				$catChildren = $this->getTreeCategories($item['parentcat']);
				if ($catChildren) $tChildren = true;
			}
			if (($item['show_footer'] && $item['footer_html']!='') || ($item['show_header'] && $item['header_html']!='') ||  ($item['show_left_sidebar'] && $item['left_sidebar_html']!='') || ($item['show_right_sidebar'] && $item['right_sidebar_html']!='') || ($item['show_content'] && ((($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') && (isset($item['children']) && count($item['children'])>0)) || ($item['content_type'] == 'content' && $item['content_html']!=''))) || ($item['content_type'] == 'parentcat' && $tChildren) ) {
				$level++;
				$subClass = $subStyle = $subAttr = '';

				if ($item['sub_width']!='') {
					$subStyle .= 'width:' . $item['sub_width'] . ';';
					$subAttr .= 'data-width="' . $item['sub_width'] . '"';
				}

				if (isset($item['dropdown_bgcolor']) && $item['dropdown_bgcolor']) $subStyle .= 'background-color:' . $item['dropdown_bgcolor'] . ';';
				if (isset($item['dropdown_bgimage']) && $item['dropdown_bgimage']) {
					if (!$item['dropdown_bgpositionx']) $item['dropdown_bgpositionx'] = 'center';
					if (!$item['dropdown_bgpositiony']) $item['dropdown_bgpositiony'] = 'center';
					$dropdown_bgimage = $this->getImageLink($item['dropdown_bgimage']);
					$subStyle .= 'background: url(\'' . $dropdown_bgimage. '\') ' . $item['dropdown_bgimagerepeat'] . ' ' . $item['dropdown_bgpositionx'] . ' ' . $item['dropdown_bgpositiony'] . ' ' . $item['dropdown_bgcolor'] . ';' ;
				}
				if (isset($item['dropdown_inlinecss']) && $item['dropdown_inlinecss']) $subStyle .= $item['dropdown_inlinecss'];

				if (isset($item['dynamic'])) {
					$subClass .= ' content-wrapper';
				}

				if (!isset($item['dynamic'])) {
					$subClass .= ' submenu';
					if ($item['is_group']) {
						$subClass .= ' dropdown-mega';
					} else {
						$subClass .= ' dropdown-menu';
					}
				}

				if (isset($item['animation_in'])) {
					$subClass .= ' animated ';
					$subClass .= $item['animation_in'];
					if ($item['animation_in']) {
						$subAttr .= ' data-animation-in="' . $item['animation_in'] . '"';
					}
					if ($item['animation_time']) {
						$subStyle .= 'animation-duration: ' . $item['animation_time'] . 's;-webkit-animation-duration: ' . $item['animation_time'] . 's;';
					}
				}

				if ($subClass!='') $subClass = 'class="' . $subClass . '"';
				if ($subStyle!='') $subStyle = 'style="' . $subStyle . '"';

				if (!isset($item['dynamic'])) {
					$html .= '<div ' . $subClass . ' ' . $subAttr . ' ' . $subStyle . '>';
				}

				// $html .= '<div class="drilldown-back"><a href="#"><span class="drill-opener"></span><span class="current-cat"></span></a></div>';
                $html .= '<div class="submenu-inner before-ves-submenu-inner">';

				// TOP BLOCK
				if ($item['show_header'] && $item['header_html']!='') {
					$html .= '<div class="item-header">' . $this->decodeWidgets($item['header_html']) . '</div>';
				}

				if ($item['show_left_sidebar'] || $item['show_content'] || $item['show_right_sidebar']) {

					if (!isset($item['dynamic'])) {
						$html .= '<div class="content-wrapper">';
					} else {
						$html .= '<div ' . $subClass . ' ' . $subAttr . ' ' . $subStyle . '>';
					}

					$left_sidebar_width  = isset($item['left_sidebar_width'])?$item['left_sidebar_width']:0;
					$content_width       = $item['content_width'];
					$right_sidebar_width = isset($item['right_sidebar_width'])?$item['right_sidebar_width']:0;

					// LEFT SIDEBAR BLOCK
					if ($item['show_left_sidebar'] && $item['left_sidebar_html']!='') {
						if ($left_sidebar_width) $left_sidebar_width = 'style="width:' . $left_sidebar_width . '"';

						$html .= '<div class="item-sidebar left-sidebar" ' . $left_sidebar_width . '>' . $this->decodeWidgets($item['left_sidebar_html']) . '</div>';
					}
					// MAIN CONTENT BLOCK
					if ($item['show_content'] && ((($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') && $hasChildren) || $item['content_type'] == 'parentcat' || ($item['content_type'] == 'content' && $item['content_html']!=''))) {

						$html .= '<div class="item-content" ' . ($content_width==''?'':'style="width:' . $content_width . '"') . '>';

						// Content HTML
						if ($item['content_type'] == 'content' && $item['content_html']!='') {
							$html .= '<div class="nav-dropdown">' . $this->decodeWidgets($item['content_html']) . '</div>';
						}

						// Dynamic Tab
						if ($item['content_type'] == 'dynamic' && $hasChildren) {
							$column = (int)$item['child_col'];
							$html .= '<div class="level' . $level . ' nav-dropdown ves-column' . $column . '">';
							$sort_type = isset($item['submenu_sorttype'])?$item['submenu_sorttype']:'normal';
							$children = $this->sortMenuItems($item['children'], $sort_type);
							$i = $z = 0;
							$total = count($children);

							if((!$this->isMobileDevice() && $this->isCheckMobileDevice()) || !$this->isCheckMobileDevice()) {
								$html .= '<div class="dorgin-items ' . (isset($item['tab_position'])?'dynamic-' . $item['tab_position']:'') . ' row hidden-sm hidden-xs">';

								$html .= '<div class="dynamic-items ';
								if (!isset($item['tab_position']) || (isset($item['tab_position']) && $item['tab_position'] != 'top')) {
									$html .= 'col-xs-3';
								}

								$html .= ' hidden-xs hidden-sm">';

								$html .= '<ul>';
								foreach ($children as $it) {
									$attr1 = '';
									if ($it['show_icon'] && $it['icon_position']=='left' && $it['icon']!='') {
										$attr1 .= ' data-hovericon="' . $it['hover_icon'] . '" data-iconsrc="' . $it['icon'] . '"';
									}

									if ($it['caret']) {
										$attr1 .= ' data-hovercaret="' . $it['hover_caret'] . '" data-caret="' . $it['caret'] . '"';
									}

									$iClass = 'nav-item';
									if ($z==0) {
										$iClass .= ' dynamic-active';
									}
									if ($iClass) {
										$iClass = 'class="' . $iClass . '"';
									}
									$html .= '<li ' . $iClass . ' data-dynamic-id="' . $it['htmlId'] . '" ' . $attr1 . '>';
									$html .= $this->drawAnchor($it, $level);
									$html .= '</li>';
									$i++;
									$z++;
								}

								$html .= '</ul>';
								$html .= '</div>';
								$html .= '<div class="dynamic-content ';
								if (!isset($item['tab_position']) ||  (isset($item['tab_position']) && $item['tab_position'] != 'top')) {
									$html .= 'col-xs-9';
								}
								$html .= ' hidden-xs hidden-sm">';

								$z = 0;
								foreach ($children as $it) {
									if ($z==0) {
										$it['class'] = 'dynamic-active';
									}
									$it['dynamic'] = true;
									$html .= $this->filter($this->drawItem($it, $level, $i, false, $parentIndex, $disable_mobile));
									$i++;
									$z++;
								}
								$html .= '</div>';
								$html .= '</div>';
							}
							if(($this->isMobileDevice()  && $this->isCheckMobileDevice()) || !$this->isCheckMobileDevice()) {
								$html   .= '<div class="orgin-items hidden-lg hidden-md">';
								$i      = 0;
								$column = 1;
								foreach ($children as $it) {
									$html .= '<div class="mega-col col-sm-' . (12/$column) . ' mega-col-' . $i . ' mega-col-level-' . $level . '">';
									$html .= $this->filter($this->drawItem($it, $level, $i, false, $parentIndex, false));
									$html .= '</div>';
									$i++;
								}
								$html .= '</div>';
							}

							$html .= '</div>';
						}
						// Child item
						if (($item['content_type'] == 'childmenu' && $hasChildren) || $item['content_type'] == 'parentcat') {
							$column   = (int)$item['child_col'];
							$grid_type = isset($item['child_col_type'])?$item['child_col_type']:"normal";//bootstrap|normal
							$column_size = 12;
							if($grid_type == "bootstrap" && $column > 1){
								$column_size = 12 / $column;
							}
							$custom_class = "";

							$sort_type = isset($item['submenu_sorttype'])?$item['submenu_sorttype']:'normal';

							if($item['content_type'] == 'parentcat'){
								$isgroup_level = isset($item['isgroup_level'])?(int)$item['isgroup_level']:0;
								$custom_class = 'content-type-parentcat';
								$it_level = 1;
								$list = [];
								$max_level = 100;
								$catChildren = $this->getTreeCategories($item['parentcat'],$it_level,$list,$max_level,$isgroup_level, $sort_type);
								$children = $this->sortMenuItems($catChildren, $sort_type);
							} else{
								$children = $this->sortMenuItems($item['children'], $sort_type);
							}

							$html     .= '<div class="level' . $level . ' nav-dropdown ves-column' . $column .' '.$custom_class. '">';

							$i        = 0;
							$total    = count($children);
							$resultTmp = [];
							$x1 = 0;
							$levelTmp =1;

							$resultTmpSort = [];
							$childIndex = 1;
							foreach ($children as  $z => $it) {
								if($grid_type == "bootstrap") {
									$parentChildIndex = $parentIndex."-".$childIndex;
									$resultTmp[] = $this->drawItem($it, $level, $i, false, $parentChildIndex, $disable_mobile);
									$i++;
									$childIndex++;
								} else {
									//$resultTmp[$x1][$levelTmp] = $this->drawItem($it, $level, $i, false);
									$resultTmpSort[$x1][$levelTmp] = 1;
									if ($x1==$column-1 || $i == (count($children)-1)) {
										$levelTmp++;
										$x1=0;
									} else {
										$x1++;
									}
									$i++;
								}
							}

							if($resultTmpSort) {
								$index2 = 0;
								$childIndex = 1;
								foreach($resultTmpSort as $_k2 => $_v2) {
									if($_v2) {
										$index3 =0;
										foreach($_v2 as $_k3 => $_v3){
											if(isset($children[$index2])) {
												$parentChildIndex = $parentIndex."-".$childIndex;
												$resultTmp[$_k2][$index3] = $this->drawItem($children[$index2], $level, $_k3, false, $parentChildIndex, $disable_mobile);
												$index3++;
												$childIndex++;
											}
											$index2++;
										}
									}
								}
							}

							if($generate_mobile_menu_code){
								$html .= '<div class="item-content1 '.self::$_hidden_menu_content_1.'">';
							}else{
								$html .= '<div class="item-content1">';
							}
							$i2 = $i3 = 0;
							foreach ($resultTmp as $k1 => $v1) {
								if($grid_type == "bootstrap") {
									$i2++;
									$i3++;
									switch ($column) {
										case 5:
											if($i3 <= 3) {
												$column_size = 3;
											} elseif($i3 == 4) {
												$column_size = 2;
											} elseif($i3 == 5) {
												$column_size = 1;
											}
											break;
										case 7:
											if($i3 <= 5) {
												$column_size = 2;
											} else{
												$column_size = 1;
											}
											break;
										case 8:
											if($i3 <= 4) {
												$column_size = 2;
											} else{
												$column_size = 1;
											}
											break;
										case 9:
											if($i3 <= 3) {
												$column_size = 2;
											} else{
												$column_size = 1;
											}
											break;
										case 10:
											if($i3 <= 2) {
												$column_size = 2;
											} else{
												$column_size = 1;
											}
											break;
										case 11:
											if($i3 <= 1) {
												$column_size = 2;
											} else{
												$column_size = 1;
											}
											break;
									}
									if($i2 == 1 || ($i2-1) % $column == 0){
										$html .= '<div class="mega-row row">';
									}

									$html .= '<div class="mega-col mega-col-' . $column_size . ' col-sm-'.$column_size.' mega-col-level-' . $level . ' col-xs-12">';
										$html .= $v1;
										$html .= '</div>';

									if( $i2%$column == 0 || $i2==count($resultTmp) ) {
										$html .= '</div>';
										$i3 = 0;
									}

								} else {
									$html .= '<div class="mega-col mega-col-' . $i . ' mega-col-level-' . $level . ' col-xs-12">';
									foreach ($v1 as $k2 => $v2) {
										$html .= $v2;
									}
									$html .= '</div>';
								}
							}
							$html .= '</div>';
							if($generate_mobile_menu_code){
								$html .= '<div class="item-content2 '.self::$_hidden_menu_content_2.'">';
								foreach ($children as  $z => $it) {
									$it['htmlId'] = isset($it['htmlId'])?($it['htmlId'].'2'):(time().rand());
									$html .= $this->filter($this->drawItem($it, $level, $i, false, $parentIndex, false));
								}
								$html .= '</div>';
							}
							$html .= '</div>';
						}

						$html .= '</div>';
					}

					// RIGHT SIDEBAR BLOCK
					if ($item['show_right_sidebar'] && $item['right_sidebar_html']!='') {
						if ($right_sidebar_width) $right_sidebar_width = 'style="width:' . $right_sidebar_width . '"';
						$html .= '<div class="item-sidebar right-sidebar" ' . $right_sidebar_width . '>' . $this->decodeWidgets($item['right_sidebar_html']) . '</div>';
					}

					$html .= '</div>';
				}

				// BOTTOM BLOCK
				if ($item['show_footer'] && $item['footer_html']!='') {
					$html .= '<div class="item-footer">' . $this->decodeWidgets($item['footer_html']) . '</div>';
				}

                $html .= '</div>';

				if (!isset($item['dynamic'])) {
					$html .= '</div>';
				}
			}

			if (isset($item['after_html']) && $item['after_html']) {
				$html .= '<div class="item-after-content">' . $item['after_html'] . '</div>';
			}

			if ($listTag) {
				$html .= '</li>';
			} else {
				$html .= '</div>';
			}
		} catch (\Exception $e) {

		}
		return $html;
	}

    /**
     * decode img
     *
     * @param string $src
     * @return string
     */
	public function decodeImg($str)
    {
        if (empty($str)) return "";

		$orginalStr    = $str;
		$count         = substr_count($str, "<img");
		//$mediaUrl      = $this->mediaUrl;
		$firstPosition = 0;
		for ($i=0; $i < $count; $i++) {
			if ($firstPosition==0) $tmp = $firstPosition;
			if ($tmp>strlen($str)) continue;
			$firstPosition = strpos($str, "<img", $tmp);
			$nextPosition = strpos($str, "/>", $firstPosition);
			$tmp = $nextPosition;
			if (!strpos($str, "<img")) continue;
			$length = $nextPosition - $firstPosition;
			$img = substr($str, $firstPosition, $length+2);
			if (!strpos($img, $this->_storeManager->getStore()->getBaseUrl())) {
				continue;
			}

			$newImg = $this->filter($img);
			$f = strpos($newImg, 'src="', 0)+5;
			$n = strpos($newImg, '"', $f+5);
			$src = substr($newImg, $f, ($n-$f));
			if (!strpos($img, 'placeholder.gif')) {
				$src1 = '';
				if (strpos($newImg, '___directive')) {
					$e = strpos($newImg, '___directive', 0) + 13;
					$e1 = strpos($newImg, '/key', 0);
					$src1 = substr($newImg, $e, ($e1-$e));
					$src1 = base64_decode($src1);
				} else {
					$mediaP = strpos($src, "wysiwyg", 0);
					$src1 = substr($src, $mediaP);
					$src1 = '{{media url="' . $src1 . '"}}';
				}
				if($src1) {
					$orginalStr = str_replace($src, $src1, $orginalStr);
					$newImg = str_replace($src, $src1, $newImg);
				}
			}
		}
		return $orginalStr;
	}

    /**
     * Decode url
     *
     * @param string $src
     * @return string
     */
	public function decodeUrl($str)
    {
		$orginalStr    = $str;
		$count         = substr_count($str, "<a");
		//$baseUrl      = $this->baseUrl;
		$firstPosition = 0;
		for ($i=0; $i < $count; $i++) {
			if ($firstPosition==0) $tmp = $firstPosition;
			if ($tmp>strlen($str)) continue;
			$firstPosition = strpos($str, "<a", $tmp);
			$nextPosition = strpos($str, ">", $firstPosition);
			$tmp = $nextPosition;
			if (!strpos($str, "<a")) continue;
			$length = $nextPosition - $firstPosition;
			$link = substr($str, $firstPosition, $length+2);
			if (!strpos($link, $this->_storeManager->getStore()->getBaseUrl())) {
				continue;
			}

			$newLink = $this->filter($link);
			$f = strpos($newLink, 'href="', 0)+6;
			$n = strpos($newLink, '"', $f+5);
			$src = substr($newLink, $f, ($n-$f));
			$src1 = '';
			$src2 = '';
			if (strpos($src, '___directive')) {
				$newLinkLength = strlen($src);
				$e = strpos($src, '___directive', 0) + 13;
				$e1 = strpos($src, '/key', 0);
				$src1 = substr($src, $e, ($e1-$e));
				$src1 = base64_decode($src1);
				$tmp_src = substr($src, ($e1-$e), ($newLinkLength - 1));
				if($tmp_src) {
					$tmp_arr = explode("//", $tmp_src);
					if(count($tmp_arr) > 1) {
						$src2 = $tmp_arr[1];
					}
				}
			}

			if($src1) {
				if($src2){
					$src1 .="/".$src2;
				}
				$orginalStr = str_replace($src, $src1, $orginalStr);
				$newLink = str_replace($src, $src1, $newLink);
			}

		}
		return $orginalStr;
	}

    /**
     * Sort menu items
     *
     * @param array|mixed $submenu_items
     * @param string $sort_type
     * @return mixed|array
     */
	public function sortMenuItems($submenu_items, $sort_type = "normal")
    {
		if($sort_type == "alphabet") {
			usort($submenu_items, function ($item1, $item2) {
				$item_name_1 = substr($item1['name'], 0, 1);
				$item_name_2 = substr($item2['name'], 0, 1);
				if ($item_name_1==$item_name_2) return 0;
				return $item_name_1 < $item_name_2 ? -1 : 1;
			});
		}
		return $submenu_items;
	}

    /**
     * Get root category
     *
     * @param bool $is_return_collection
     * @return mixed|array
     */
	public function getRootCategory($is_return_collection = false)
    {
		if (!$this->_catsCollection) {
			$menuCategories = $this->getMenuCategories();
			if($is_return_collection) {
				$this->_catsCollection  = $this->storeCategories->getCategoryCollectionByIds($menuCategories, null, $this->getCurrentStore()->getId());
			} else {
				$cats   = $this->storeCategories->getCategoriesCollection($menuCategories, null, $this->getCurrentStore()->getId());
				$rootId = $this->getCurrentStore()->getRootCategoryId();
				if ($cats) {
					foreach ($cats as $cat) {
						if ($cat['value'] == $rootId) {
							$this->_catsCollection = $cat;
							break;
						}
					}
				} else {
					$this->_catsCollection = [];
				}
			}
		}
		return $this->_catsCollection;
	}

    /**
     * Get all categories
     *
     * @param string $sort_type
     * @param int $limit
     * @return mixed|object
     */
	public function getAllCategory($sort_type = 'normal', $limit = 0)
    {
		if (!$this->_cats) {
			$this->_cats = $this->_categoryFactory->create()->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('include_in_menu',1)
			->addAttributeToFilter('is_active','1');
			if($sort_type == 'alphabet') {
				$this->_cats->setOrder('name','ASC');
				$this->_cats->getSelect()->order('name ASC');
			} else {
				$this->_cats->setOrder('position','ASC');
				$this->_cats->getSelect()->order('position ASC');
			}
			if($limit) {
				$this->_cats->setPageSize((int)$limit)->setCurPage(1);
			}
            $store = $this->_storeManager->getStore();
			if($store && $this->_cats instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection) {
				$this->_cats->setStore($store);
			}
		}
		return $this->_cats;
	}

    /**
     * Get category by id
     *
     * @param int $catId
     * @param string|mixed $cat
     * @return mixed|object|string
     */
	public function getCategory($catId, $cat = '')
    {
		$catId = (int) $catId;
		$category = '';
		if(!isset($this->_cached_category_links[$catId])) {
			if ($cat == '') {
				$cat = $this->getRootCategory();
			}

			if ($cat) {
				if ((int) $cat['value'] == (int) $catId) {
					$this->_cached_category_links[$catId] = $cat['category'];
					return $cat['category'];
				} else if (isset($cat['children']) && is_array($cat['children'])) {
					foreach ($cat['children'] as $catChild) {
						$category = $this->getCategory($catId, $catChild);
						if ($category) {
							$this->_cached_category_links[$catId] = $category;
							break;
						}
					}
				}
			}

 		} else {
 			$category = $this->_cached_category_links[$catId];
 		}

		return $category;
	}

    /**
     * Check category is has products
     *
     * @param int $category_id
     * @return bool
     */
	protected function _hasProducts($category_id)
    {
	    $products = $this->_categoryFactory->create()->load($category_id)
	        ->getProductCollection()
	        ->addAttributeToSelect('entity_id')
	        ->addAttributeToFilter('status', 1)
	        ->addAttributeToFilter('visibility', 4);
	    return ( $products->count() > 0 )  ? true : false;
	}

    /**
     * Get all tree categories
     *
     * @param int $level
     * @param array $list
     * @param string $sort_type
     * @param bool $_getimage
     * @param string $_image_field
     * @param int $limit
     * @return mixed|array
     */
	public function getAllTreeCategories($level = 0, $list = [], $sort_type = 'normal', $_getimage = false, $_image_field = "image", $limit = 0) {
		$cats     = $this->getAllCategory($sort_type, $limit);
		foreach($cats as $category) {
				$category->setStoreId($this->_storeManager->getStore()->getId());
				$tmp                   = [];
				$tmp["name"]           = $category->getName();
				$tmp['link_type']      = 'custom_link';
				$tmp['link']           = $category->getUrl();
				$tmp['show_footer']    = $tmp['show_header'] = $tmp['show_left_sidebar'] = $tmp['show_right_sidebar'] = 0;
				$tmp['show_content']   = 1;
				$tmp['content_width']  = $tmp['sub_width'] = '100%';
				$tmp['color']          = '';
				$tmp['show_icon']      = $tmp['is_group'] = false;
				$tmp['content_type']   = 'childmenu';
				$tmp['target']         = '_self';
				$tmp['align']          = 3;
				$tmp['child_col']      = 1;
				$tmp['status']         = 1;
				$tmp['disable_bellow'] = 0;
				$tmp['classes']        = '';
				$tmp['child_col_type'] = 'normal';
				$tmp['parent_id']	   = $category->getParentId();
				$tmp['id']             = $category->getId();
				$tmp['position']       = $category->getPosition();
				if($_getimage) {
					$_image_field = $_image_field?$_image_field:'image';
					$tmp['image']  = $category->getData($_image_field);
				}

				if($urls = parse_url($tmp['link'])){
					$url_host = isset($urls['host'])?$urls['host']:"";
					$base_url = $this->_storeManager->getStore()->getBaseUrl();
					if($url_host && ($base_urls = parse_url($base_url))) {
						$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
						if($url_host != $base_urls['host']){
							$tmp['link'] = str_replace($url_host, $base_urls['host'], $tmp['link']);
						}
					}
				}

				//$tmp['children']       = $this->getAllTreeCategories((int)$level + 1,$category->getId(), $list);

				$list[] = $tmp;
		}

		return $list;
	}

    /**
     * Get tree categories
     *
     * @param int $parentId
     * @param int $level
     * @param array|mixed $list
     * @param string $max_level
     * @param int $isgroup_level
     * @param string $sort_type
     * @param bool $_getimage
     * @param string $_image_field
     * @param int $limit
     * @return mixed|array
     */
	public function getTreeCategories($parentId, $level = 0, $list = [], $max_level = 100, $isgroup_level = 0, $sort_type = 'normal', $_getimage=false, $_image_field="image", $limit = 0)
    {
		if(!$this->_category_list) {
			$list = [];
			$this->_category_list = $this->getAllTreeCategories($level, $list, $sort_type, $_getimage, $_image_field, $limit);
		}
		if($this->_category_list){
			foreach($this->_category_list as $_cat) {
				if ($_cat['parent_id'] == $parentId) {
					$tmp = $_cat;
					$next_level = (int)$level + 1;
					if($isgroup_level && $next_level <= $isgroup_level) {
						$tmp['is_group'] = true;
					}
					if($next_level < $max_level) {
						$tmp['children']       = $this->getTreeCategories($_cat['id'], (int)$level + 1, [], $max_level, $isgroup_level, $sort_type, $_getimage, $_image_field, $limit );
					}
					if($sort_type=="alphabet" && isset($tmp['children'])){
						$tmp['children'] = $this->sortMenuItems($tmp['children'], $sort_type);
					}

					$list[] = $tmp;
				}
			}
		}
		return $list;
	}

    /**
     * Convert categories to menu items
     *
     * @param mixed|array $cats
     * @param bool $_getimage
     * @param string $_image_field
     * @return mixed|array
     */
	public function convertCategoriesToMenuItems($cats = [], $_getimage = false, $_image_field = "image")
    {
		$list = [];
		if($cats) {
			foreach($cats as $category) {
					$category->setStoreId($this->_storeManager->getStore()->getId());
					$tmp                   = [];
					$tmp["name"]           = $category->getName();
					$tmp['link_type']      = 'custom_link';
					$tmp['link']           = $category->getUrl();
					$tmp['show_footer']    = $tmp['show_header'] = $tmp['show_left_sidebar'] = $tmp['show_right_sidebar'] = 0;
					$tmp['show_content']   = 1;
					$tmp['content_width']  = $tmp['sub_width'] = '100%';
					$tmp['color']          = '';
					$tmp['show_icon']      = $tmp['is_group'] = false;
					$tmp['content_type']   = 'childmenu';
					$tmp['target']         = '_self';
					$tmp['align']          = 3;
					$tmp['child_col']      = 1;
					$tmp['status']         = 1;
					$tmp['disable_bellow'] = 0;
					$tmp['classes']        = '';
					$tmp['child_col_type'] = 'normal';
					$tmp['parent_id']	   = $category->getParentId();
					$tmp['id']             = $category->getId();
					$tmp['position']       = $category->getPosition();
					if($_getimage) {
						$_image_field = $_image_field?$_image_field:'image';
						$tmp['image']  = $category->getData($_image_field);
					}

					if($urls = parse_url($tmp['link'])){
						$url_host = isset($urls['host'])?$urls['host']:"";
						$base_url = $this->_storeManager->getStore()->getBaseUrl();
						if($url_host && ($base_urls = parse_url($base_url))) {
							$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
							if($url_host != $base_urls['host']){
								$tmp['link'] = str_replace($url_host, $base_urls['host'], $tmp['link']);
							}
						}
					}

					$list[] = $tmp;
			}
		}
		return $list;
	}

    /**
     * Init menu items
     *
     * @param mixed|object|array $catChildren
     * @param mixed|array|int[] $catIds
     * @return mixed|array
     */
	public function initMenuItems($catChildren, $catids = []) {
		$parents = [];
		$list = [];
		foreach($catChildren as $index => $_cat) {
			if(!in_array($_cat['id'], $catids)){
				continue;
			}
			if ($_cat['parent_id'] && !isset($parents[$_cat['parent_id']])) {
				$tmp2 = [];
				$tmp2['hasChildren'] = 0;
				$tmp2['id'] = $_cat['id'];
				$tmp2['menu'] = [];
				$tmp2['menu'][] = $index;
				$parents[$_cat['parent_id']] = $tmp2;
			} elseif(isset($parents[$_cat['parent_id']])) {
				$parents[$_cat['parent_id']]['menu'][] = $index;
			}
			$tmp = $_cat;
			$list[$index] = $tmp;
		}
		foreach($list as $index => $_cat){
			if(isset($parents[$_cat['id']])){
				$parents[$_cat['id']]['hasChildren'] = 1;
			}
		}
		foreach($parents as $index => $_parent) {
			if(!$_parent['hasChildren'] && $_parent['menu']) {
				foreach($_parent['menu'] as $k=>$menu_index) {
					$list[$menu_index]['parent_id'] = 1;
				}
			}
		}
		$list = $this->convertListToTree($list, 1, [], 0);
		return $list;
	}

    /**
     * Convert list to tree
     *
     * @param mixed|array $catChildren
     * @param int $parentId
     * @param mixed|array $list
     * @param int $level
     * @return mixed|array
     */
	public function convertListToTree($catChildren, $parentId = 1, $list= [], $level = 0)
    {
		foreach($catChildren as $index => $_cat) {
			if ($_cat['parent_id'] == $parentId) {
				$tmp = $_cat;
				$next_level = (int)$level + 1;
				$tmp['children']       = $this->convertListToTree($catChildren, $_cat['id'], [], (int)$level + 1);
				$list[] = $tmp;
			}
		}
		return $list;
	}

    /**
     * Get current url
     *
     * @return string
     */
	public function getCurrentUrl()
    {
        $currentUrl = $this->_url->getCurrentUrl();
        $currentUrl = explode("?", $currentUrl);
        $currentUrl = $currentUrl[0];
        if ($this->endsWith($currentUrl, "/")) {
        	$currentUrl = substr_replace($currentUrl, "", -1);
        }
        return $currentUrl;
	}

    /**
     * Check ends with
     *
     * @param string $haystack
     * @param string $needle
     * @return bool|int
     */
	public function endsWith($haystack, $needle)
    {
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}

    /**
     * Escape quotes in java script
     *
     * @param  mixed  $data
     * @param  string $quote
     * @return mixed
     */
    public function jsQuoteEscape($data, $quote = '\'')
    {
    	if (is_array($data)) {
    		$result = [];
    		foreach ($data as $item) {
                if (is_string($item)) {
                    $result[] = str_replace($quote, '\\'.$quote, $item);
                } else {
                    $result[] = $item;
                }
    		}

    		return $result;
    	}

    	return str_replace($quote, '\\'.$quote, $data);

    }

    /**
     * Get menu categories
     *
     * @return mixed|object|array|null
     */
    public function getMenuCategories()
    {
    	return $this->menuCategories;
    }

    /**
     * Set menu categories
     *
     * @param mixed|array|object|null $categories
     * @return $this
     */
    public function setMenuCategories($categories)
    {
    	$this->clearCategoryCollection();
    	$this->menuCategories = $categories;
    	return $this;
    }

    /**
     * Clear category collection
     *
     * @return void
     */
    public function clearCategoryCollection()
    {
       $this->_catsCollection = null;
    }

    /**
     * Reset data
     *
     * @return $this
     */
    public function resetData()
    {
    	$this->clearCategoryCollection();
    	$this->_category_list = null;
    	$this->menuCategories = [];
    	return $this;
    }

    /**
     * Is mobile device
     *
     * @return bool|innt
     */
    public function isMobileDevice()
    {
    	if(!isset($this->_is_mobile_device)){
    		$this->_is_mobile_device = $this->_mobileDetect->isMobile();
    	}
        return $this->_is_mobile_device;
    }

    /**
     * Check mobile device
     *
     * @return bool|int
     */
   	public function isCheckMobileDevice()
    {
    	if (!isset($this->_is_checked_mobile)) {
    		$is_check_mobile_device = $this->getConfig("general_settings/enable_check_mobile");
    		$is_check_mobile_device = $is_check_mobile_device?(int)$is_check_mobile_device:0;
    		$this->_is_checked_mobile = $is_check_mobile_device;
    	}
    	return $this->_is_checked_mobile;
    }

    /**
     * Check is enabled module
     *
     * @return bool|int
     */
    public function isEnabled()
    {
        $isEnabled = $this->getConfig("general_settings/enabled");
    	return $isEnabled != null && is_numeric($isEnabled) ? (bool)$isEnabled: true;
    }

    /**
     * Check is enabled module
     *
     * @param int|string|null $storeId
     * @return string
     */
    public function getDefaultMenuAlias($storeId = null)
    {
        return $this->getConfig("general_settings/default_alias", $storeId);
    }

    /**
	 * @param string $directory
	 * @param string $relativeFileName
	 * @param string $contents
	 * @return void
	 */
    public function df_file_write($directory, $relativeFileName, $contents)
    {
		/** @var \Magento\Framework\App\ObjectManager $om */
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		/** @var \Magento\Framework\Filesystem $filesystem */
		$filesystem = $om->get('Magento\Framework\Filesystem');
		/** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
		$writer = $filesystem->getDirectoryWrite($directory);
		/** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
		$file = $writer->openFile($relativeFileName, 'w');
		try {
			$file->lock();
			try {
				$file->write($contents);
			}
			finally {
				$file->unlock();
			}
		}
		finally {
			$file->close();
		}
	}

    /**
     * @param $data_array
     * @return array
     */
    public function xss_clean_array($data_array)
    {
        $result = [];
        if (is_array($data_array)) {
            foreach ($data_array as $key => $val) {
                $val = $this->xss_clean($val);
                $result[$key] = $val;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return string|string[]|null
     */
    public function xss_clean($data)
    {
        if (!is_string($data)) {
            return $data;
        }
        // Fix &entity\n;
        $data = str_replace(['&amp;', '&lt;', '&gt;'], ['&amp;amp;', '&amp;lt;', '&amp;gt;'], $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...',
            $data
        );

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>',
            $data
        );

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
                '',
                $data
            );
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
}
