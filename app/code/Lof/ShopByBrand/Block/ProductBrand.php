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

class ProductBrand extends \Magento\Framework\View\Element\Template
{
    /**
     * @var _brandFactory
     */
    protected $_brandFactory;
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\ShopByBrand\Model\BrandFactory $brandFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_brandFactory = $brandFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function getImageMediaPath()
    {
        return $this->getUrl('pub/media', ['_secure' => $this->getRequest()->isSecure()]);
    }

    public function getBrand()
    {
        $attribute_code = \Lof\ShopByBrand\Model\Items::ATTRIBUTE_CODE;
        $product = $this->registry->registry('current_product');
        $collection = $this->_brandFactory->create()->getCollection();
        $collection->addFieldToFilter('attribute_id', $product->getData($attribute_code));
        return $collection->getFirstItem();
        ;
    }
}
