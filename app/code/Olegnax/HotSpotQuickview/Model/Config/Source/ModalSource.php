<?php
/**
 * @author      Olegnax
 * @package     Olegnax_HotSpotQuickview
 * @copyright   Copyright (c) 2022 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\HotSpotQuickview\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ModalSource implements ArrayInterface
{
	const SOURCE_NONE = '';
    const SOURCE_PRODUCT = 'product';
	const SOURCE_CUSTOM = 'custom';
	const SOURCE_URL = 'url';
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
			['value' => static::SOURCE_PRODUCT, 'label' => __('None')],
			['value' => static::SOURCE_PRODUCT, 'label' => __('Product in Quick view Modal')],
			['value' => static::SOURCE_CUSTOM, 'label' => __('Static Block (Custom Content) in Modal')],
			['value' => static::SOURCE_URL, 'label' => __('Open URL')],
        ];
    }
}
