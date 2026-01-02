<?php

namespace Olegnax\Quickview\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class addHandle implements ObserverInterface
{
    protected $scopeConfig;

    protected $request;

    protected $storeManager;

    protected $productRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Http $request,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
    }

    public function execute(Observer $observer)
    {
        $layout = $observer->getData('layout');
        $fullActionName = $observer->getData('full_action_name');

        if ($fullActionName != 'ox_quickview_catalog_product_view') {
            return $this;
        }

        $productId = $this->request->getParam('id');
        if (isset($productId)) {
            try {
                $product = $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());
            } catch (NoSuchEntityException $e) {
                return false;
            }

            $productType = $product->getTypeId();

            $layout->getUpdate()->addHandle('ox_quickview_catalog_product_view_type_' . $productType);
            if ($this->getSystemValue('olegnax_quickview/general/hide_tabs')) {
                $layout->getUpdate()->addHandle('ox_quickview_hide_tabs');
            } else {
				if ($this->getSystemValue('olegnax_quickview/general/move_tabs')) {
					$layout->getUpdate()->addHandle('ox_quickview_move_tabs');
				} 
			}			
            if ($this->getSystemValue('olegnax_quickview/general/hide_reviews')) {
                $layout->getUpdate()->addHandle('ox_quickview_hide_reviews');
            } 
            if ($this->getSystemValue('olegnax_quickview/general/hide_desc')) {
                $layout->getUpdate()->addHandle('ox_quickview_hide_desc');
            } 
            if ($this->getSystemValue('olegnax_quickview/general/link_title')) {
                $layout->getUpdate()->addHandle('ox_quickview_link_title');
            } 
            if ($this->getSystemValue('olegnax_quickview/general/hide_sku')) {
                $layout->getUpdate()->addHandle('ox_quickview_hide_sku');
            } 
            if ($this->getSystemValue('olegnax_quickview/general/hide_related')) {
                $layout->getUpdate()->addHandle('ox_quickview_hide_related');
            } 
            if ($this->getSystemValue('olegnax_quickview/general/hide_upsell')) {
                $layout->getUpdate()->addHandle('ox_quickview_hide_upsell');
            } 
            if ($this->getSystemValue('olegnax_quickview/general/force_fotorama')) {
                $layout->getUpdate()->addHandle('ox_quickview_force_fotorama');
            } 
        }

        return $this;
    }

    public function getSystemValue($path, $storeCode = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeCode);
    }
}
