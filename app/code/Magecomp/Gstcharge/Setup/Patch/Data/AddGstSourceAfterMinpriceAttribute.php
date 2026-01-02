<?php
namespace Magecomp\Gstcharge\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class AddGstSourceAfterMinpriceAttribute implements DataPatchInterface
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
            'gst_source_after_minprice',
            [
                'group' => 'Indian GST',
                'label' => 'GST Rate If Product Price Below Minimum Set Price',
                'type'  => 'varchar',
                'input' => 'select',
                'required' => false,
                'sort_order' => 7,
                'searchable' => false,
                'filterable' => false,
                'global' => Attribute::SCOPE_STORE,
                'source' => '\Magecomp\Gstcharge\Model\Source\Percentage',
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
