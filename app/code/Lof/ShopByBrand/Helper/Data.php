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

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_request;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_request            = $context->getRequest();
    }
    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @return string|null
     */
    public function getConfig($key)
    {
        $result = $this->scopeConfig->getValue('lof_shopbybrand/'.$key);
        return $result;
    }
    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @return string|null
     */
    public function getBrandConfig($key)
    {
        $result = $this->scopeConfig->getValue('vesbrand/'.$key);
        return $result;
    }
    public function isEnabledSidebar()
    {
        return (int)$this->getConfig("general/enabled_sidebar");
    }
    public function isAutoSync()
    {
        return (int)$this->getConfig("general/is_auto_sync");
    }
    public function getSearchFormUrl()
    {
        $url        = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = __($this->getBrandConfig('general_settings/url_prefix'));
        $url_suffix = $this->getBrandConfig('general_settings/url_suffix');
        $urlPrefix  = '';
        if ($url_prefix) {
            $urlPrefix = $url_prefix . '/';
        }
        return $url . $urlPrefix . 'search';
    }
    public function getSearchKey()
    {
        return $this->_request->getParam('s');
    }
}
