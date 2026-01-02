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
namespace Webkul\PaymentShippingRestriction\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Config as Config;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class CreateAttributes implements
    SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavConfig $eavConfig,
        EavSetupFactory $eavSetupFactory,
        Config $config
    ) {
        $this->eavConfig = $eavConfig;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->config=$config;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        $entityType = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY);
        $entityTypeId = $entityType->getId();
        $attributeSetId = $entityType->getDefaultAttributeSetId();
        $attributeGroupId =  $this->config->getAttributeGroupId($attributeSetId, 'Product Details');
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_shipping_attribute',
            [
                'type' => 'text',
                'group' => '',
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'frontend' => '',
                'label' => 'Select Shipping',
                'input' => 'multiselect',
                'class' => '',
                'source' => \Webkul\PaymentShippingRestriction\Model\Config\Source\ShippingOptions::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,configurable',
                'group' => 'Product Details'

            ]
        );
        $attributeId = $eavSetup->getAttributeId($entityTypeId, 'product_shipping_attribute');
        $eavSetup->addAttributeToSet(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            $attributeId
        );
          /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
          $eavSetup->addAttribute(
              \Magento\Catalog\Model\Product::ENTITY,
              'product_payment_attribute',
              [
                'type' => 'varchar',
                'group' => '',
                'backend' => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'frontend' => '',
                'label' => 'Select Payment',
                'input' => 'multiselect',
                'class' => '',
                'source' => \Webkul\PaymentShippingRestriction\Model\Config\Source\PaymentOptions::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => false,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple,virtual,configurable,downloadable',
                'group' => 'Product Details'
              ]
          );
        $attributeId = $eavSetup->getAttributeId($entityTypeId, 'product_payment_attribute');

        $eavSetup->addAttributeToSet(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            $attributeId
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_shipping_attribute',
            [
                'type'                => 'varchar',
                'group'               => 'General Information',
                'input'               => 'multiselect',
                'label'               => __('Select Shipping Method'),
                'note'                => __('Select Shipping Method'),
                'backend'             => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'source'              => \Webkul\PaymentShippingRestriction\Model\Config\Source\ShippingOptions::class,
                'global'              => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'             => true,
                'user_defined'        => false,
                'default'             => 0,
                'required'            => false,
                "searchable"          => false,
                "filterable"          => true,
                "comparable"          => false,
                'visible_on_front'    => false,
                'unique'              => false
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_payment_attribute',
            [
                'type'                  => 'varchar',
                'group'                 => 'General Information',
                'input'                 => 'multiselect',
                'label'                 => __('Select Payment Method'),
                'note'                  => __('Select Payment Method'),
                'backend'               => \Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend::class,
                'source'                => \Webkul\PaymentShippingRestriction\Model\Config\Source\PaymentOptions::class,
                'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'               => true,
                'user_defined'          => false,
                'default'               => 0,
                'required'              => false,
                "searchable"            => false,
                "filterable"            => true,
                "comparable"            => false,
                'visible_on_front'      => false,
                'unique'                => false
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }
}
