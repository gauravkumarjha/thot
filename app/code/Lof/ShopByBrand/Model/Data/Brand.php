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

namespace Lof\ShopByBrand\Model\Data;

use Lof\ShopByBrand\Api\Data\BrandInterface;

class Brand extends \Magento\Framework\Api\AbstractExtensibleObject implements BrandInterface
{

    /**
     * {@inheritdoc}
     */
    public function getBrandId()
    {
        return $this->_get(self::BRAND_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setBrandId($brandId)
    {
        return $this->setData(self::BRAND_ID, $brandId);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lof\ShopByBrand\Api\Data\BrandExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return $this->_get(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnail()
    {
        return $this->_get(self::THUMBNAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTitle()
    {
        return $this->_get(self::PAGE_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageTitle($page_title)
    {
        return $this->setData(self::PAGE_TITLE, $page_title);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->_get(self::META_KEYWORDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($meta_keywords)
    {
        return $this->setData(self::META_KEYWORDS, $meta_keywords);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->_get(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($meta_description)
    {
        return $this->setData(self::META_DESCRIPTION, $meta_description);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationTime()
    {
        return $this->_get(self::CREATION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationTime($creation_time)
    {
        return $this->setData(self::CREATION_TIME, $creation_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId()
    {
        return $this->_get(self::ATTRIBUTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeId($attribute_id)
    {
        return $this->setData(self::ATTRIBUTE_ID, $attribute_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeOptionId()
    {
        return $this->_get(self::ATTRIBUTE_OPTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeOptionId($attribute_option_id)
    {
        return $this->setData(self::ATTRIBUTE_OPTION_ID, $attribute_option_id);
    }

   /**
    * {@inheritdoc}
    */
    public function getUpdateTime()
    {
        return $this->_get(self::UPDATE_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdateTime($update_time)
    {
        return $this->setData(self::UPDATE_TIME, $update_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageLayout()
    {
        return $this->_get(self::PAGE_LAYOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageLayout($page_layout)
    {
        return $this->setData(self::PAGE_LAYOUT, $page_layout);
    }

    /**
     * {@inheritdoc}
     */
    public function getLayoutUpdateXml()
    {
        return $this->_get(self::LAYOUT_UPDATE_XML);
    }

    /**
     * {@inheritdoc}
     */
    public function setLayoutUpdateXml($layout_update_xml)
    {
        return $this->setData(self::LAYOUT_UPDATE_XML, $layout_update_xml);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatured()
    {
        return $this->_get(self::FEATURED);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeatured($featured)
    {
        return $this->setData(self::FEATURED, $featured);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($store)
    {
        return $this->setData(self::STORE_ID, $store);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreCode()
    {
        return $this->_get(self::STORE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreCode($store)
    {
        return $this->setData(self::STORE_CODE, $store);
    }
}
