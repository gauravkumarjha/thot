<?php
declare(strict_types=1);
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
namespace Bss\GA4\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class Attribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $attributeFactory;

    /**
     * @param CollectionFactory $attributeFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
    ) {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllAtrribute()[1];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getAllAtrribute()[0];
    }

    /**
     * Get all attribute
     *
     * @return array
     */
    public function getAllAtrribute()
    {
        $type = [
            'text',
            'multiselect',
            'select'
        ];
        $array = $arrays = [];
        $attributeInfo = $this->attributeFactory->create();
        foreach ($attributeInfo as $items) {
            if ($items->getFrontendLabel() && $items->getUsedInProductListing() && in_array($items->getFrontendInput(), $type)) {
                $array[$items->getAttributeCode()] = $items->getFrontendLabel();
                $item['value'] = $items->getAttributeCode();
                $item['label'] = $items->getFrontendLabel();
                $arrays[] = $item;
            }
        }
        return [$array, $arrays];
    }

    /**
     * Get attribute label
     *
     * @param string $code
     * @return string|void
     */
    public function getAttributeLabelByCode($code)
    {
        $attributeInfo = $this->attributeFactory->create();
        foreach ($attributeInfo as $items) {
            if ($items->getFrontendLabel() && $items->getAttributeCode() == $code) {
                return $items->getFrontendLabel();
            }
        }
        return '';
    }
}
