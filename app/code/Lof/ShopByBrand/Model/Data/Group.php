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

use Lof\ShopByBrand\Api\Data\GroupInterface;

class Group extends \Magento\Framework\Api\AbstractExtensibleObject implements GroupInterface
{

    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * Set group_id
     * @param string $groupId
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\ShopByBrand\Api\Data\GroupExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\ShopByBrand\Api\Data\GroupExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\ShopByBrand\Api\Data\GroupExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->_get(self::URL_KEY);
    }

    /**
     * Set url_key
     * @param string $urlKey
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * Get position
     * @return int|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Set position
     * @param int $position
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Get status
     * @return int|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param int $status
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get shown_in_sidebar
     * @return int|null
     */
    public function getShownInSidebar()
    {
        return $this->_get(self::SHOWN_IN_SIDEBAR);
    }

    /**
     * Set shown_in_sidebar
     * @param int $shown_in_sidebar
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setShownInSidebar($shown_in_sidebar)
    {
        return $this->setData(self::SHOWN_IN_SIDEBAR, $shown_in_sidebar);
    }
}
