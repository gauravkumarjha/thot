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
 * @copyright  Copyright (c) 2022-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GA4\Block\CatalogWidget\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    /**
     * Set template product list
     *
     * @return string
     */
    public function getTemplate()
    {
        return 'Bss_GA4::product/widget/content/grid.phtml';
    }

    /**
     * Make a different between widgets
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->getWidgetHelper()->getIndex();
    }

    /**
     * Pre-pare list items data
     *
     * @param Collection $collection
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function prepareListItems($collection)
    {
        return $this->getWidgetHelper()->prepareListItems($collection, $this->getTitle());
    }

    /**
     * Convert data to String
     *
     * @param string|int|float|array $data
     * @return bool|string
     */
    public function serializer($data)
    {
        return $this->getWidgetHelper()->serializer($data);
    }

    /**
     * Check module is enable
     *
     * @return mixed
     */
    public function isEnableModule()
    {
        return $this->getWidgetHelper()->isEnableModule();
    }

    /**
     * Get widget helper
     *
     * @return void
     */
    public function getWidgetHelper()
    {
        return;
    }
}
