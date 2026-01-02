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
namespace Lof\ShopByBrand\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

class BrandSaveAfter implements ObserverInterface
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

     /**
      * @var \Magento\Framework\App\RequestInterface
      */
    protected $_request;

    protected $_helperAttribute;

    protected $_helperData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Lof\ShopByBrand\Helper\Attribute $helperAttribute,
        \Lof\ShopByBrand\Helper\Data $helperData
    ) {
        $this->_resource = $resource;
        $this->_coreRegistry = $coreRegistry;
        $this->_request = $request;
        $this->_helperAttribute = $helperAttribute;
        $this->_helperData = $helperData;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helperData->isAutoSync()) {

            $_brand = $observer->getEvent()->getBrand();
            $brandId = $observer->getEvent()->getBrandId();
            $isNew = $observer->getEvent()->getIsNew();
            if ($_brand) {
                $brandId = $_brand->getId();
                if ($brandId) {
                    $this->_helperAttribute->syncAttributeForBrand($brandId, $_brand);
                }
            }
        }
    }
}
