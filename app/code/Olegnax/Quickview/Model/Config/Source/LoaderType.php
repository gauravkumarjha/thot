<?php
/**
 * @author      Olegnax
 * @package     Olegnax_Quickview
 * @copyright   Copyright (c) 2022 Olegnax (http://olegnax.com/). All rights reserved.
 * @license     Proprietary License https://olegnax.com/license/
 * @noinspection PhpDeprecationInspection
 */
namespace Olegnax\Quickview\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class LoaderType implements ArrayInterface
{
    const TYPE_THEME = '-theme';
    const TYPE_MAGENTO = '-magento';
    const TYPE_SPINNER = '-spinner';
	const TYPE_RECT_OUTLINE = '-outline-rect';
    const TYPE_CUSTOM = '-custom';

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => static::TYPE_MAGENTO, 'label' => __('Magento Default')],
            ['value' => static::TYPE_SPINNER, 'label' => __('Spinner')],
			['value' => static::TYPE_RECT_OUTLINE, 'label' => __('Outline Rectangle (Athlete2 Style)')],
            ['value' => static::TYPE_THEME, 'label' => __('Theme Preloader')],
            ['value' => static::TYPE_CUSTOM, 'label' => __('Custom Image')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            static::TYPE_MAGENTO => __('Magento Default'),
            static::TYPE_SPINNER => __('Spinner'),
			static::TYPE_RECT_OUTLINE => __('Outline Rectangle (Athlete2 Style)'),
            static::TYPE_THEME => __('Theme Preloader'),
            static::TYPE_CUSTOM => __('Custom Image')
        ];
    }
}