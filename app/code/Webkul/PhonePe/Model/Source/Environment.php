<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PhonePe
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PhonePe\Model\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    public const ENVIRONMENT_PRODUCTION = 'PRODUCTION';
    public const ENVIRONMENT_SANDBOX = 'UAT';

    /**
     * Possible environment types.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_SANDBOX,
                'label' => __('UAT Sandbox'),
            ],
            [
                'value' => self::ENVIRONMENT_PRODUCTION,
                'label' => __('PRODUCTION'),
            ],
        ];
    }
}
