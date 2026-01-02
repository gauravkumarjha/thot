<?php

namespace DiMedia\ShippingDayUnicomerce\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get shipping days based on store code
     *
     * Example:
     *  - 'usd' store → 12 days
     *  - 'inr' store → 6 days
     */
    public function getShippingDays()
    {
        $storeCode = $this->storeManager->getStore()->getCode();

        switch ($storeCode) {
            case 'usd':
                $extraDays = 12;
                break;
            default:
                $extraDays = 6;
                break;
        }

        return $extraDays;
    }

    /**
     * Get formatted text like "Shipped in 2 weeks"
     */
    public function getShippingText()
    {
        $days = $this->getShippingDays();
        $weeks = ceil($days / 7);
        return __('Shipped in %1 week(s)', $weeks);
    }
}
