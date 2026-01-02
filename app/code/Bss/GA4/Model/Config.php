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

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config extends AbstractHelper
{
    public const XML_PATH_ENABLED = "bss_ga4/general/enable";
    public const XML_PATH_MEASUREMENT_ID = "bss_ga4/general/measurement_id";
    public const XML_PATH_PRODUCT_IDENTIFIER = "bss_ga4/general/product_identifier";
    public const XML_PATH_ENABLE_BRAND = "bss_ga4/general/enable_brand";
    public const XML_PATH_BRAND_ATTRIBUTE = "bss_ga4/general/brand_attribute";
    public const XML_PATH_ENABLE_AFFILIATION = "bss_ga4/general/enable_affiliation";
    public const XML_PATH_AFFILIATION_ATTRIBUTE = "bss_ga4/general/affiliation_attribute";
    public const XML_PATH_BUNDLE_PRICE = "bss_ga4/general/bundle_price";
    public const XML_PATH_EXCLUDE_SHIPPING_TRANSACTION = "bss_ga4/general/exclude_shipping_transaction";
    public const XML_PATH_EXCLUDE_ORDERS_ZERO_VALUE = "bss_ga4/general/exclude_orders_zero_value";

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Get value config
     *
     * @param string $config
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($config, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $config,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Module is enable
     *
     * @return mixed
     */
    public function enableModule()
    {
        return $this->getConfigValue(self::XML_PATH_ENABLED);
    }

    /**
     * Get measurement id
     *
     * @return mixed
     */
    public function getMeasurementId()
    {
        return $this->getConfigValue(self::XML_PATH_MEASUREMENT_ID);
    }

    /**
     * Get item id
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getItemId($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_PRODUCT_IDENTIFIER, $storeId);
    }

    /**
     * Get brand item
     *
     * @param int $storeId
     * @return mixed|string
     */
    public function getItemBrand($storeId = null)
    {
        if ($this->getConfigValue(self::XML_PATH_ENABLE_BRAND, $storeId)) {
            return $this->getConfigValue(self::XML_PATH_BRAND_ATTRIBUTE, $storeId);
        }
        return false;
    }

    /**
     * Get affiliation
     *
     * @param int $storeId
     * @return mixed|string
     */
    public function getItemAffiliation($storeId = null)
    {
        if ($this->getConfigValue(self::XML_PATH_ENABLE_AFFILIATION, $storeId)) {
            return $this->getConfigValue(self::XML_PATH_AFFILIATION_ATTRIBUTE, $storeId);
        }
        return false;
    }

    /**
     * Get config exclude zero order
     *
     * @return mixed
     */
    public function getConfigExcludeOrder()
    {
        if ($this->getConfigValue(self::XML_PATH_EXCLUDE_ORDERS_ZERO_VALUE)) {
            return $this->getConfigValue(self::XML_PATH_EXCLUDE_ORDERS_ZERO_VALUE);
        }
        return '';
    }

    /**
     * Get config exclude shipping transaction
     *
     * @return mixed
     */
    public function getConfigExcludeShippingTransaction()
    {
        if ($this->getConfigValue(self::XML_PATH_EXCLUDE_SHIPPING_TRANSACTION)) {
            return $this->getConfigValue(self::XML_PATH_EXCLUDE_SHIPPING_TRANSACTION);
        }
        return '';
    }

    /**
     * Get config price bundle product
     *
     * @return mixed
     */
    public function getConfigTypePriceBundle()
    {
        if ($this->getConfigValue(self::XML_PATH_BUNDLE_PRICE)) {
            return $this->getConfigValue(self::XML_PATH_BUNDLE_PRICE);
        }
        return '';
    }
}
