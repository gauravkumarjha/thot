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

class Sidebar extends \Magento\Framework\View\Element\Template
{

    protected $_brandFactory;

    protected $_helperData;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\ShopByBrand\Model\BrandFactory $brandFactory,
        \Lof\ShopByBrand\Helper\Data $helperData
    ) {
        $this->_brandFactory = $brandFactory;
        $this->_helperData = $helperData;
        parent::__construct($context);
    }


    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function _toHtml()
    {
        if ($this->_helperData->isEnabledSidebar()) {
            return parent::_toHtml();
        }
        return "";
    }

    public function getBrands()
    {
        $collection = $this->_brandFactory->create()->getCollection();
        $collection->addFieldToFilter('status', \Lof\ShopByBrand\Model\Status::STATUS_ENABLED);
        $collection->setOrder('name', 'ASC');
        $charBarndArray = [];
        foreach ($collection as $brand) {
            $name = trim($brand->getName());
            $charBarndArray[strtoupper($name[0])][] = $brand;
        }

        return $charBarndArray;
    }
}
