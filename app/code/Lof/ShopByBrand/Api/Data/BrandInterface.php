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
declare(strict_types=1);

namespace Lof\ShopByBrand\Api\Data;

interface BrandInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const NAME = 'name';
    const BRAND_ID = 'brand_id';
    const GROUP_ID = 'group_id';
    const URL_KEY = 'url_key';
    const DESCRIPTION = 'description';
    const IMAGE = 'image';
    const THUMBNAIL = 'thumbnail';
    const PAGE_TITLE = 'page_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const CREATION_TIME  = 'creation_time';
    const ATTRIBUTE_ID = 'attribute_id';
    const ATTRIBUTE_OPTION_ID = 'attribute_option_id';
    const UPDATE_TIME = 'update_time';
    const PAGE_LAYOUT = 'page_layout';
    const LAYOUT_UPDATE_XML = 'layout_update_xml';
    const STATUS = 'status';
    const FEATURED = 'featured';
    const POSITION = 'position';
    const STORE_ID = 'store_id';
    const STORE_CODE = 'store_code';

    /**
     * Get brand_id
     * @return string|null
     */
    public function getBrandId();

    /**
     * Set brand_id
     * @param string $brandId
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setBrandId($brandId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\ShopByBrand\Api\Data\BrandExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\ShopByBrand\Api\Data\BrandExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\ShopByBrand\Api\Data\BrandExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setName($name);

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Set url_key
     * @param string $urlKey
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setUrlKey($urlKey);

    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param string $groupId
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setGroupId($groupId);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setDescription($description);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setImage($image);

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setThumbnail($thumbnail);

    /**
     * Get page_title
     * @return string|null
     */
    public function getPageTitle();

    /**
     * Set page_title
     * @param string $page_title
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setPageTitle($page_title);

    /**
     * Get meta_keywords
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Set meta_keywords
     * @param string $meta_keywords
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setMetaKeywords($meta_keywords);

    /**
     * Get meta_description
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set meta_description
     * @param string $meta_description
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setMetaDescription($meta_description);

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Set creation_time
     * @param string $creation_time
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setCreationTime($creation_time);

    /**
     * Get attribute_id
     * @return int|null
     */
    public function getAttributeId();

    /**
     * Set attribute_id
     * @param int $attribute_id
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setAttributeId($attribute_id);

    /**
     * Get attribute_option_id
     * @return int|null
     */
    public function getAttributeOptionId();

    /**
     * Set attribute_option_id
     * @param int $attribute_option_id
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setAttributeOptionId($attribute_option_id);

    /**
     * Get update_time
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set update_time
     * @param string $update_time
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setUpdateTime($update_time);

    /**
     * Get page_layout
     * @return string|null
     */
    public function getPageLayout();

    /**
     * Set page_layout
     * @param string $page_layout
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setPageLayout($page_layout);

    /**
     * Get layout_update_xml
     * @return string|null
     */
    public function getLayoutUpdateXml();

    /**
     * Set layout_update_xml
     * @param string $layout_update_xml
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setLayoutUpdateXml($layout_update_xml);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setStatus($status);

    /**
     * Get featured
     * @return int|null
     */
    public function getFeatured();

    /**
     * Set featured
     * @param int $featured
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setFeatured($featured);

    /**
     * Get position
     * @return string|null
     */
    public function getPosition();

    /**
     * Set position
     * @param int $position
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setPosition($position);

    /**
     * Get store
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set store
     * @param int $store
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setStoreId($store);

    /**
     * Get store
     * @return string|null
     */
    public function getStoreCode();

    /**
     * Set store
     * @param string $store
     * @return \Lof\ShopByBrand\Api\Data\BrandInterface
     */
    public function setStoreCode($store);
}
