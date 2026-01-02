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
namespace Bss\GA4\Plugin\Block\Product;

use Bss\GA4\Model\WidgetProduct;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class SearchResult
{
    /**
     * @var WidgetProduct
     */
    protected $widgetProduct;

    /**
     * @param WidgetProduct $widgetProduct
     */
    public function __construct(
        \Bss\GA4\Model\WidgetProduct $widgetProduct
    ) {
        $this->widgetProduct = $widgetProduct;
    }

    /**
     * Set collection when M2 call Product collection
     *
     * @param ListProduct $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterGetLoadedProductCollection(\Magento\Catalog\Block\Product\ListProduct $subject, $result)
    {
        $this->widgetProduct->setWidgetCollection([
            'collection' => $result,
            'title' => $subject->getTitle()
        ]);
        return $result;
    }
}
