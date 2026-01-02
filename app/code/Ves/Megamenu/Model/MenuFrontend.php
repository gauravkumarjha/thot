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

namespace Ves\Megamenu\Model;

use Ves\Megamenu\Api\Data\MenuFrontendInterface;

class MenuFrontend extends Menu implements MenuFrontendInterface
{
    /**
     * Generate menu tree
     *
     * @param int $storeId = 0
     * @param bool $allowChangeState = false
     * @return $this
     */
    public function generateMenuTree($storeId = 0, $allowChangeState = false)
    {
        //getnerate menu
        if ($this->getId()) {
            //Write code at here
            $tree_array = [];
            $data = $this->menuHelper;
            $menuItems  = $this->getMenuItems();
            $structure  = json_decode($this->getStructure(), true);
            $categories = [];
            if ($menuItems && is_array($menuItems)) {
                foreach ($menuItems as $item) {
                    if (isset($item['link_type']) && $item['link_type'] == 'category_link' && isset($item['category']) && !in_array($item['category'], $categories)) {
                        $categories[] = $item['category'];
                    }
                }
            }
            $data->setCurrentStoreId($storeId);
            $data->setMenuCategories($categories);
            if ($allowChangeState) {
                $data->setAllowChangeState(true);
            }
            if (is_array($structure)) {
                $i = 1;
                foreach ($structure as $k => $v) {
                    $itemData = $data->renderMenuItemData($v, [], $menuItems);
                    $tree_array[] = $data->drawItemForTree($itemData, 0, 1, true, $i);
                    $i++;
                }
            }
            if ($tree_array) {
                $this->setMenuTree($tree_array);
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDesignStyle()
    {
        return $this->getData(self::DESIGN_STYLE);
    }

    /**
     * @inheritdoc
     */
    public function setDesignStyle($design_style)
    {
        return $this->setData(self::DESIGN_STYLE, $design_style);
    }

    /**
     * @inheritdoc
     */
    public function getNodes()
    {
        return $this->getData(self::NODES);
    }

    /**
     * @inheritdoc
     */
    public function setNodes($nodes)
    {
        return $this->setData(self::NODES, $nodes);
    }
}
