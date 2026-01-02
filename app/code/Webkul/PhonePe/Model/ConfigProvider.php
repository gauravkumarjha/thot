<?php

/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PhonePe
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited ( https://webkul.com )
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PhonePe\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Webkul\PhonePe\Model\PaymentMethod;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Webkul\PhonePe\Helper\Data
     */
    protected $helper;

    /**
     * @param \Webkul\PhonePe\Helper\Data $helper
     */
    public function __construct(
        \Webkul\PhonePe\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Get configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $isActive = $this->helper->getConfigValue('active');
        if (!$isActive) {
            return [];
        }
        $clientId = $this->helper->getConfigValue('client_id');
        $clientSecret = $this->helper->getConfigValue('client_secret');
        $clientVersion = $this->helper->getConfigValue('client_version');
        $title = $this->helper->getConfigValue('title');
       
        return [
            'payment' => [
                PaymentMethod::METHOD_CODE => [
                    'isActive' => true,
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'clientVersion' => $clientVersion,
                    'title' => $title,
                    'code' => PaymentMethod::METHOD_CODE
                ]
            ]
        ];
    }
}
