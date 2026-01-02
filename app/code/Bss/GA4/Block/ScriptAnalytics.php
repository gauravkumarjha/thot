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
namespace Bss\GA4\Block;

use Bss\GA4\Model\Config;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class ScriptAnalytics extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Bss\GA4\Model\Config $config,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->config = $config;
    }

    /**
     * Is enable
     *
     * @return mixed
     */
    public function isEnable()
    {
        return $this->config->getConfigValue(Config::XML_PATH_ENABLED);
    }

    /**
     * Get measurement id
     *
     * @return mixed
     */
    public function getMeasurementId()
    {
        return $this->config->getConfigValue(Config::XML_PATH_MEASUREMENT_ID);
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }
}
