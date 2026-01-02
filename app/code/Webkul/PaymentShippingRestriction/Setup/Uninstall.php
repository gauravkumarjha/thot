<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PaymentShippingRestriction\Setup;
 
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Db\Select;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface as UninstallInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $_mdSetup;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $_mdSetup
    ) {
    
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_mdSetup = $_mdSetup;
    }

    /**
     * uninstall shipping and payment attributes
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_mdSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'product_payment_attribute');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'product_shipping_attribute');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_shipping_attribute');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_payment_attribute');
    }
}
