<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2022 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Plugin\App\Http;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Ves\Megamenu\Helper\Data;

/**
 * Plugin on \Magento\Framework\App\Http\Context
 */
class ContextMobileDetectPlugin
{

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Construct ContextMobileDetectPlugin
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * \Magento\Framework\App\Http\Context::getVaryString is used by Magento to retrieve unique identifier for selected context,
     * so this is a best place to declare custom context variables
     *
     * @param Context $subject
     * @return void
     */
    public function beforeGetVaryString(Context $subject)
    {
        $defaultAgeContext = 0;
        $ageContext = $defaultAgeContext;
        if ($this->helperData->isMobileDevice()) {
            $ageContext = 1;
        }
        $subject->setValue('CONTEXT_MOBILE', $ageContext, $defaultAgeContext);
    }
}
