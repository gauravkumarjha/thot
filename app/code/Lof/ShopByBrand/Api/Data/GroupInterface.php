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

interface GroupInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const NAME = 'name';
    const GROUP_ID = 'group_id';
    const URL_KEY = 'url_key';
    const POSITION = 'position';
    const STATUS = 'status';
    const SHOWN_IN_SIDEBAR = 'shown_in_sidebar';

    /**
     * Get group_id
     * @return int|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param int $groupId
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setGroupId($groupId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\ShopByBrand\Api\Data\GroupExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\ShopByBrand\Api\Data\GroupExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\ShopByBrand\Api\Data\GroupExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setName($name);

    /**
     * Get name
     * @return int|null
     */
    public function getPosition();

    /**
     * Set name
     * @param int $position
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setPosition($position);

    /**
     * Get name
     * @return int|null
     */
    public function getStatus();

    /**
     * Set name
     * @param int $status
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setStatus($status);

    /**
     * Get name
     * @return int|null
     */
    public function getShownInSidebar();

    /**
     * Set name
     * @param int $shown_in_sidebar
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setShownInSidebar($shown_in_sidebar);

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Set url_key
     * @param string $urlKey
     * @return \Lof\ShopByBrand\Api\Data\GroupInterface
     */
    public function setUrlKey($urlKey);
}
