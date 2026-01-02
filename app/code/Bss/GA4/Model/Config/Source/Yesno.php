<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Bss\GA4\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class Yesno implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'id', 'label' => __('ID')], ['value' => 'sku', 'label' => __('SKU')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['id' => __('ID'), 'sku' => __('SKU')];
    }
}
