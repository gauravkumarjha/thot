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

namespace Lof\ShopByBrand\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Company\Model\CompanyFactory;
use Magento\Company\Model\ResourceModel\Company\CollectionFactory;
use Magento\Company\Model\CompanyRepository;
use Lof\ShopByBrand\Helper\Attribute;

class UpgradeData implements UpgradeDataInterface
{
    protected $_helperAttribute;

    public function __construct(
        Attribute $helperAttribute
    ) {
        $this->_helperAttribute               = $helperAttribute;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $this->_helperAttribute->processSyncBrandAttribute();
        }
    }
}
