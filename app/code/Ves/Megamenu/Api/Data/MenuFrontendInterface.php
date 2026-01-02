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
 * @copyright  Copyright (c) 2019 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Megamenu\Api\Data;

interface MenuFrontendInterface
{
    const NODES = 'nodes';
    const DESIGN_STYLE = 'design_style';

    /**
     * Get menu_id
     * @return int|null
     */
    public function getMenuId();

    /**
     * Set menu_id
     * @param int $menu_id
     * @return $this
     */
    public function setMenuId($menuId);

    /**
     * Get desktop_template
     * @return string|null
     */
    public function getDesktopTemplate();

    /**
     * Set desktop_template
     * @param string $desktop_template
     * @return $this
     */
    public function setDesktopTemplate($desktop_template);

    /**
     * Get mobile_template
     * @return string|null
     */
    public function getMobileTemplate();

    /**
     * Set mobile_template
     * @param string $mobile_template
     * @return $this
     */
    public function setMobileTemplate($mobile_template);

    /**
     * Get scrolltofixed
     * @return int|null
     */
    public function getScrolltofixed();

    /**
     * Set scrolltofixed
     * @param int $scrolltofixed
     * @return $this
     */
    public function setScrolltofixed($scrolltofixed);

    /**
     * Get alias
     * @return string|null
     */
    public function getAlias();

    /**
     * Set alias
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias);

    /**
     * Get disable_bellow
     * @return string|null
     */
    public function getDisableBellow();

    /**
     * Set disable_bellow
     * @param string $disable_bellow
     * @return $this
     */
    public function setDisableBellow($disable_bellow);

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCretionTime();

    /**
     * Set creation_time
     * @param string $creation_time
     * @return $this
     */
    public function setCretionTime($creation_time);

    /**
     * Get update_time
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set update_time
     * @param string $update_time
     * @return $this
     */
    public function setUpdateTime($update_time);

    /**
     * Get disable_iblocks
     * @return string|null
     */
    public function getDisableIblocks();

    /**
     * Set disable_iblocks
     * @param string $disable_iblocks
     * @return $this
     */
    public function setDisableIblocks($disable_iblocks);

    /**
     * Get event
     * @return string|null
     */
    public function getEvent();

    /**
     * Set event
     * @param string $event
     * @return $this
     */
    public function setEvent($event);

    /**
     * Get classes
     * @return string|null
     */
    public function getClasses();

    /**
     * Set classes
     * @param string $classes
     * @return $this
     */
    public function setClasses($classes);

    /**
     * Get width
     * @return string|null
     */
    public function getWidth();

    /**
     * Set width
     * @param string $width
     * @return $this
     */
    public function setWidth($width);

    /**
     * Get scrolltofix
     * @return string|null
     */
    public function getScrolltofix();

    /**
     * Set scrolltofix
     * @param string $scrolltofix
     * @return $this
     */
    public function setScrolltofix($scrolltofix);

    /**
     * Get current_version
     * @return string|null
     */
    public function getCurrentVersion();

    /**
     * Set current_version
     * @param string $current_version
     * @return $this
     */
    public function setCurrentVersion($current_version);

    /**
     * Get mobile_menu_alias
     * @return string|null
     */
    public function getMobileMenuAlias();

    /**
     * Set mobile_menu_alias
     * @param string $mobile_menu_alias
     * @return $this
     */
    public function setMobileMenuAlias($mobile_menu_alias);

    /**
     * Get store ids
     * @return int[]|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param int[] $store_id
     * @return $this
     */
    public function setStoreId($store_id);

    /**
     * Get customer_group_ids ids
     * @return int[]|null
     */
    public function getCustomerGroupIds();

    /**
     * Set customer_group_ids
     * @param int[] $customer_group_ids
     * @return $this
     */
    public function setCustomerGroupIds($customer_group_ids);

    /**
     * Get version_id
     * @return string[]|null
     */
    public function getVersionId();

    /**
     * Set version_id
     * @param string[] $version_id
     * @return $this
     */
    public function setVersionId($version_id);

    /**
     * Get revert_next
     * @return string|null
     */
    public function getRevertNext();

    /**
     * Set revert_next
     * @param string $revert_next
     * @return $this
     */
    public function setRevertNext($revert_next);

    /**
     * Get revert_previous
     * @return string|null
     */
    public function getRevertPrevious();

    /**
     * Set revert_previous
     * @param string $revert_previous
     * @return $this
     */
    public function setRevertPrevious($revert_previous);

    /**
     * Get nodes
     * @return \Ves\Megamenu\Api\Data\MenuTreeItemInterface[]|null
     */
    public function getNodes();

    /**
     * Set nodes
     * @param \Ves\Megamenu\Api\Data\MenuTreeItemInterface[] $nodes
     * @return $this
     */
    public function setNodes($nodes);

    /**
     * Get design_style
     * @return \Ves\Megamenu\Api\Data\DesignStyleInterface|null
     */
    public function getDesignStyle();

    /**
     * Set design_style
     * @param \Ves\Megamenu\Api\Data\DesignStyleInterface $design_style
     * @return $this
     */
    public function setDesignStyle($design_style);

}
