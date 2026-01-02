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

namespace Lof\ShopByBrand\Block;

/**
 * Class Navigation
 *
 * @package Lof\ShopByBrand\Block
 */
class Navigation extends \Magento\LayeredNavigation\Block\Navigation
{
    // public function __construct(
    //     \Magento\Framework\View\Element\Template\Context $context,
    //     \Lof\ShopByBrand\Model\Layer\Resolver $layerResolver,
    //     \Magento\Catalog\Model\Layer\FilterList $filterList,
    //     \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
    //     array $data = []
    // ) {
    //     parent::__construct($context, $layerResolver, $filterList,
    //         $visibilityFlag);
    // }

    /**
     * Navigation constructor.
     * Fixes: 2.4.1
     *
     * @param \Magento\Framework\View\Element\Template\Context       $context
     * @param \Lof\ShopByBrand\Model\Layer\Resolver                  $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList                $filterList
     * @param \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag
     * @param array                                                  $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\ShopByBrand\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $filterList, $visibilityFlag, $data);
    }
}
