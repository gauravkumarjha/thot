<?php

namespace Olegnax\Quickview\Plugin;

use Closure;
use Magento\Catalog\Block\Product\View\GalleryOptions;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\ScopeInterface;

class BlockProductViewGallery
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var  Http
     */
    protected $request;

    /**
     * ResultPage constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Http $request
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param GalleryOptions $subject
     * @param Closure $proceed
     * @param array $name
     * @param string|null $module
     *
     * @return string|false
     */
    public function aroundGetVar(
        GalleryOptions $subject,
        Closure $proceed,
        $name,
        $module = null
    ) {
        $result = $proceed($name, $module);

        $isEnabled = $this->getSystemValue('olegnax_quickview/general/enable');

        if (!$isEnabled || $this->request->getFullActionName() != 'ox_quickview_catalog_product_view') {
            return $result;
        }

        switch ($name) {
            /*
            case "gallery/fullscreen/navdir" :
                $result = 'horizontal';
                break;*/
            /* Disable the image fullscreen on quickview*/
            case "gallery/allowfullscreen" :
                $result = false;
                break;
            case 'gallery/nav':
                $result = false;
                break;
        }

        return $result;
    }

    public function getSystemValue($path, $storeCode = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeCode);
    }
}
