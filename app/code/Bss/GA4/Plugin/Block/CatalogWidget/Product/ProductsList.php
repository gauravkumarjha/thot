<?php
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
 * @copyright  Copyright (c) 2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GA4\Plugin\Block\CatalogWidget\Product;

use Bss\GA4\Model\GA4WidgetHelper;

class ProductsList
{
    /**
     * @var GA4WidgetHelper
     */
    protected $widgetHelper;

    /**
     * @param GA4WidgetHelper $widgetHelper
     */
    public function __construct(
        \Bss\GA4\Model\GA4WidgetHelper $widgetHelper
    ) {
        $this->widgetHelper = $widgetHelper;
    }

    /**
     * @param \Bss\GA4\Block\CatalogWidget\Product\ProductsList $subject
     * @param $result
     * @return GA4WidgetHelper
     */
    public function afterGetWidgetHelper(
        \Bss\GA4\Block\CatalogWidget\Product\ProductsList $subject,
                                                          $result
    ) {
        return $this->widgetHelper;
    }
}
