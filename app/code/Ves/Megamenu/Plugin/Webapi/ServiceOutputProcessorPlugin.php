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

class ServiceOutputProcessorPlugin
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * Construct ServiceOutputProcessorPlugin
     *
     * @param FilterProvider $filterProvider
     */
    public function __construct(FilterProvider $filterProvider)
    {
        $this->filterProvider = $filterProvider;
    }

    /**
     * before Process
     *
     * @param ServiceOutputProcessor $subject
     * @param mixed|array|object $data
     * @param string|mixed $serviceClassName
     * @param string|mixed $serviceMethodName
     * @return mixed|array
     */
    public function beforeProcess(
        ServiceOutputProcessor $subject,
        $data,
        $serviceClassName,
        $serviceMethodName
    ) {
        if ($serviceClassName == 'Magento\Cms\Api\PageRepositoryInterface' && $serviceMethodName == 'getById') {
            $content = $data->getContent();
            $parsedContent = $this->filterProvider->getPageFilter()->filter($content);
            //$data->setContent($parsedContent);
            return [$data, $serviceClassName, $serviceMethodName];
        }
    }
}
