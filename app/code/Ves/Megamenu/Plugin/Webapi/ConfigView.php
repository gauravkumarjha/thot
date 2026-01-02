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
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Plugin\Webapi;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Ves\Megamenu\Helper\Data;

class ConfigView
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Construct ConfigView
     *
     * @param Data $helperData
     */
    public function __construct(Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * plugin after get media attributes
     * @param \Magento\Framework\Config\View $subject
     * @param array $result
     * @return array
     */
    public function afterGetMediaAttributes(\Magento\Framework\Config\View $subject, array $result)
    {
        if ($this->helperData->isEnabled()) {
            if (empty($result) || !isset($result["type"])) {
                $result["type"] = "small_image";
                $result["width"] = isset($result["width"]) ? $result["width"] : 240;
                $result["height"] = isset($result["height"]) ? $result["height"] : 240;
            }
        }
        return $result;
    }
}
