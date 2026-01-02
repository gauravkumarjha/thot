<?php

namespace DiMedia\OgTag\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Ves\Brand\Model\Brand as Brand;

class SocialMeta extends Template
{
    protected $pageConfig;
    protected $registry;
    protected $urlInterface;
    protected $imageHelper;
    protected $Brand;

    public function __construct(
        Context $context,
        PageConfig $pageConfig,
        Registry $registry,
        UrlInterface $urlInterface,
        ImageHelper $imageHelper,
        ImageHelper $Brand,
        array $data = []
    ) {
        $this->pageConfig = $pageConfig;
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        $this->urlInterface = $urlInterface;
        $this->Brand = $Brand;
        parent::__construct($context, $data);
    }

    public function getMetaTitle()
    {
        $fullActionName = $this->getRequest()->getFullActionName();
        if ($fullActionName == "cms_page_view") {
            return $this->pageConfig->getTitle()->get();
        }
        return $this->pageConfig->getTitle()->get();
    }

    public function getMetaDescription()
    {
        $fullActionName = $this->getRequest()->getFullActionName();
        if ($fullActionName == "cms_page_view") {
            return $this->pageConfig->getDescription();
        }
        return $this->pageConfig->getDescription();
    }

    public function getImageUrl()
    {

        $fullActionName = $this->getRequest()->getFullActionName();
        $imageUrl = '';

        if ($fullActionName == "catalog_category_view") {
            $category = $this->registry->registry('current_category');
            if ($category) {
                $imageUrl = $this->urlInterface->getBaseUrl() . $category->getImageUrl();
            }
        } elseif ($fullActionName == "catalog_product_view") {
            $product = $this->registry->registry('current_product');
            if ($product && $product->getImage()) {
                $imageUrl = $this->imageHelper->init($product, 'product_page_image_large')->getUrl();
            }
        } elseif ($fullActionName == "vesbrand_brand_view") {
            $Brand = $this->registry->registry('current_brand');
             $imageUrl = $Brand->getImageUrl();
        } elseif ($fullActionName == "chetaru_edit_index_view") {
            $url_key = $this->getRequest()->getParam('url_key', false);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connectionDb = $resource->getConnection();
            //gives table name with prefix 
            $tableName = $resource->getTableName('chetaru_edit_post');
            //Select Data from table 
            $query = "Select featured_image FROM " . $tableName . ' WHERE url_key="' . $url_key . '"';
            $result = $connectionDb->fetchRow($query); 

            $imageUrl = $this->urlInterface->getBaseUrl().$result['featured_image'];
        }
        if (!$imageUrl) {
            // Default image if category/product image is not available
            $mediaUrl = $this->urlInterface->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
            $imageUrl = $mediaUrl . '/logo/stores/1/the-house-of-thing-logo.png';
        }

        return $imageUrl;
    }

    public function getProductSchemaJson()
    {
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return '';
        }
        if ($product && $product->getImage()) {
            $imageUrl = $this->imageHelper->init($product, 'product_page_image_large')->getUrl();
        }
        $productData["script"] = [
            '@context' => 'http://schema.org/',
            '@type' => 'Product',
            'name' => $product->getName(),
            'image' =>  $imageUrl,
            'description' => strip_tags($product->getDescription()),
            'sku' => $product->getSku(),
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $product->getCurrency(),
                'price' => $product->getFinalPrice(),
                'availability' => $product->isAvailable() ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock',
                'url' => $product->getProductUrl()
            ]
        ];
        $productData['metaTag'] = '<meta itemprop="description" content="'. strip_tags($product->getDescription()).'" />
        <meta itemprop="image" content="'. $imageUrl.'" />';

        return json_encode($productData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

}
