<?php
namespace Magecomp\Gstcharge\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class AddGstSourceAttribute implements DataPatchInterface
{
    private $moduleDataSetup;

    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            'gst_source',
            [
                'group' => 'Indian GST',
                'label' => 'GST Rate(in Percentage)',
                'type'  => 'varchar',
                'input' => 'select',
                'source' => '\Magecomp\Gstcharge\Model\Source\Percentage',
                'required' => false,
                'sort_order' => 1,
                'global' => Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );
    }

   /**
    * {@inheritdoc}
    */
    public static function getDependencies()
    {
        return [];
    }

   /**
    * {@inheritdoc}
    */
    public function getAliases()
    {
        return [];
    }
}
