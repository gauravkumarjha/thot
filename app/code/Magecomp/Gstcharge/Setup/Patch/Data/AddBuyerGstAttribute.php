<?php
namespace Magecomp\Gstcharge\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Customer\Setup\CustomerSetupFactory;

class AddBuyerGstAttribute implements DataPatchInterface
{
    private $moduleDataSetup;

    private $customerSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
    }
    
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
            $customerSetup->addAttribute(
                Customer::ENTITY,
                'buyergst',
                [
                    'label' => 'Buyer GST Number',
                    'required' => 0,
                    'system' => 0,
                    'position' => 100
                ]
            );
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'buyergst');
        
            $used_in_forms[]="adminhtml_customer";
            $used_in_forms[]="checkout_register";
            $used_in_forms[]="customer_account_create";
            $used_in_forms[]="customer_account_edit";
            $used_in_forms[]="adminhtml_checkout";
            
            $attribute->setData('used_in_forms', $used_in_forms)
                    ->setData("is_used_for_customer_segment", true)
                    ->setData("is_system", 0)
                    ->setData("sort_order", 200);
            $attribute->save();
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
