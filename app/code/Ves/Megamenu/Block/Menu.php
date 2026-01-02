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

namespace Ves\Megamenu\Block;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Context;

class Menu extends \Magento\Framework\View\Element\Template implements
\Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Ves\Megamenu\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ves\Megamenu\Model\Menu
     */
    protected $_menu;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Ves\Megamenu\Helper\MobileDetect
     */
    protected $_mobileDetect;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private $json;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ves\Megamenu\Helper\Data                        $helper
     * @param \Ves\Megamenu\Model\Menu                         $menu
     * @param \Magento\Customer\Model\Session                  $customerSession
     * @param \Ves\Megamenu\Helper\MobileDetect                $mobileDetectHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array                                            $data
     * @param Json|null                                        $json
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ves\Megamenu\Helper\Data $helper,
        \Ves\Megamenu\Model\Menu $menu,
        \Magento\Customer\Model\Session $customerSession,
        \Ves\Megamenu\Helper\MobileDetect $mobileDetectHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = [],
        Json $json = null
    ) {
        parent::__construct($context, $data);
        $this->_helper          = $helper;
        $this->_menu            = $menu;
        $this->_customerSession = $customerSession;
        $this->_mobileDetect    = $mobileDetectHelper;
        $this->httpContext = $httpContext;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->addData(
            [
                'cache_lifetime' => false,
                'cache_tags' => [\Ves\Megamenu\Model\Menu::CACHE_TAG, \Magento\Store\Model\Group::CACHE_TAG],
            ]
        );
    }

    /**
     * get customer group ID
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if(!isset($this->_customer_group_id)) {
            $this->_customer_group_id = (int)$this->_customerSession->getCustomerGroupId();
            $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
            if(!$isLoggedIn) {
               $this->_customer_group_id = 0;
            }
        }
        return $this->_customer_group_id;

    }

    /**
     * get megamenu key
     *
     * @param string $megamenu_key
     * @return string
     */
    public function getMegamenuKey($megamenu_key)
    {
        $params = $this->getRequest()->getParams();
        $params_key = "";
        if ($params) {
            $params_key = array_values($params);
            $params_key = $params_key ? implode('|', $params_key) : "";
            $params_key = $params_key ? ("-". md5($params_key) ) : "";
        }
        return $megamenu_key.$params_key;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $menuId = $this->getData('id');
        $menuId = $menuId ? $menuId : 0;
        $code = $this->getMenuAlias();

        $custom_menu_class = $this->getConfig('custom_class');
        $custom_menu_class = $custom_menu_class ? ("-".$custom_menu_class) : '';

        $megamenu_key = $code."-".$menuId.$custom_menu_class;
        $customerGroupId = (int)$this->getCustomerGroupId();
        $customerGroupId = $customerGroupId?("group".$customerGroupId):"group0";
        $megamenu_key .= "-".$customerGroupId;

        if ($this->getMobileDetect()->isMobile()) {
            $megamenu_key .= "-mobilemenu";
        }

        $shortCacheId = [
            'VES_MEGAMENU_MENU_BLOCK',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(Context::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout(),
            $this->getMegamenuKey($megamenu_key)
            //,$this->json->serialize($this->getRequest()->getParams())
        ];
        $cacheId = $shortCacheId;

        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['megamenu_key'] = $this->getMegamenuKey($megamenu_key);
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }

    /**
     * get mobile template html
     *
     * @param string $menuAlias
     * @param mixed|null $menu
     * @return string
     */
    public function getMobileTemplateHtml($menuAlias, $menu = null)
    {
        $html = '';
        if($menu) {
            $html = $this->getLayout()->createBlock('Ves\Megamenu\Block\MobileMenu')->setData('menu', $menu)->toHtml();
        } else if($menuAlias){
            $html = $this->getLayout()->createBlock('Ves\Megamenu\Block\MobileMenu')->setData('alias', $menuAlias)->toHtml();
        }

        return $html;
    }

    /**
     * get mobile detect helper
     *
     * @return \Ves\Megamenu\Helper\MobileDetect
     */
    public function getMobileDetect()
    {
        return $this->_mobileDetect;
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if ($this->_helper->isEnabled()) {
            $menu = $this->getMenuProfile($this->getData('id'), $this->getMenuAlias());
            if ($menu) {
                $customerGroups = $menu->getData('customer_group_ids');
                $customerGroupId = (int)$this->getCustomerGroupId();
                if(is_array($customerGroups) && !in_array($customerGroupId, $customerGroups)) return;

                $this->setData("menu", $menu);
            }
        }
        return parent::_beforeToHtml();
    }

    /**
     * @inheritdoc
     */
    public function _toHtml()
    {
        if (!$this->_helper->isEnabled())
            return "";

        if (!$this->getTemplate()) {
            $this->setTemplate("Ves_Megamenu::widget/menu.phtml");
        }
        return parent::_toHtml();
    }

    /**
     * Get menu profile
     *
     * @param int $menuId
     * @param string $alias
     * @return mixed|bool
     */
    public function getMenuProfile($menuId = 0, $alias = "")
    {
        $menu = false;
        $store = $this->_storeManager->getStore();
        $customerGroupId = (int)$this->getCustomerGroupId();
        if ($menuId) {
            if ($customerGroupId) {
                $menu = $this->_menu->setStore($store)
                                ->setLoggedCustomerGroupId($customerGroupId)
                                ->load((int)$menuId);
                if (!$menu->getId()) {
                    $menu = $this->_menu->setStore($store)
                                    ->load((int)$menuId);
                }
            } else {
                $menu = $this->_menu->setStore($store)->load((int)$menuId);
            }

            if ($menu->getId() != $menuId) {
                $menu = false;
            }
        } elseif ($alias) {
            if ($customerGroupId) {
                $menu = $this->_menu->setStore($store)
                                ->setLoggedCustomerGroupId($customerGroupId)
                                ->load(addslashes($alias));
                if (!$menu->getId()) {
                    $menu = $this->_menu->setStore($store)
                                    ->load(addslashes($alias));
                }
            } else {
                $menu = $this->_menu->setStore($store)->load(addslashes($alias));
            }

            if ($menu->getAlias() != $alias) {
                $menu = false;
            }
        }
        if ($menu && !$menu->getStatus()) {
            $menu = false;
        }

        return $menu;
    }

    /**
     * Get config
     *
     * @param string $key
     * @param mixed|string|int|null $default
     * @return mixed|string|int|null
     */
    public function getConfig($key, $default = NULL)
    {
        if($this->hasData($key)){
            return $this->getData($key);
        }
        return $default;
    }

    /**
     * Get default menu alias
     *
     * @return string
     */
    public function getMenuAlias()
    {
        $menuAlias = $this->getConfig('alias');
        $defaultAlias = $this->_helper->getDefaultMenuAlias();
        return !empty($menuAlias) ? $menuAlias : $defaultAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [\Ves\Megamenu\Model\Menu::CACHE_TAG, \Magento\Store\Model\Group::CACHE_TAG];
    }

    /**
     * decode json string code
     *
     * @param string $jsonString
     * @return mixed
     */
    public function decodeJson($jsonString)
    {
        return $jsonString ? $this->json->unserialize($jsonString) : null;
    }
}
