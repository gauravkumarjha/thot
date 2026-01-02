<?php

namespace DiMedia\Llms\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\State;
use Magento\Framework\Escaper;
use Psr\Log\LoggerInterface;
use Magento\Cms\Model\ResourceModel\Page\Collection;
use Chetaru\Edit\Model\ResourceModel\Post\CollectionFactory as postCollectionFactory;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory as brandCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class Index extends Action
{
    protected $storeManager;
    protected $productCollectionFactory;
    protected $escaper;
    protected $logger;
    protected $state;
    protected $pageCollection;
    protected $postCollectionFactory;
    protected $brandCollectionFactory;
    protected $categoryCollectionFactory;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CollectionFactory $productCollectionFactory,
        Escaper $escaper,
        LoggerInterface $logger,
        State $state,
        postCollectionFactory $postCollectionFactory,
        brandCollectionFactory $brandCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Collection $Collection
    ) {
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->state = $state;
        $this->pageCollection = $Collection;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        echo $currentLimit = ini_get('max_execution_time');
        if ((int)$currentLimit < 29000) {
            set_time_limit(59000);
        }
        try {
            $this->state->setAreaCode('frontend');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // Area code already set
        }

        $store = $this->storeManager->getStore();
        $baseUrl = $store->getBaseUrl();
        $currencySymbol = $store->getCurrentCurrency()->getCurrencySymbol();

        date_default_timezone_set('Asia/Kolkata');
        $filePath = BP . '/pub/llms.txt';
        $data = "Updated on: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

        $pageSize = 20;
        $currentPage = 1;
        $pageNumber = 1;
        $hasMore = true;

        while ($hasMore) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect(['name', 'url_key', 'description', 'meta_title', 'meta_description', 'price', 'sku']);
            $collection->addAttributeToFilter('status', 1);
            $collection->addAttributeToFilter('visibility', ['neq' => 1]);
            $collection->setPageSize($pageSize);
            $collection->setCurPage($currentPage);

            if (!$collection->getSize()) {
                break;
            }

            $data .= "===== PAGE " . $pageNumber . " =====" . PHP_EOL;

            foreach ($collection as $product) {
                $productUrl = $baseUrl . $product->getUrlKey() . '.html';

                $data .= "Product: " . $this->escaper->escapeHtml($product->getName()) . PHP_EOL;
                $data .= "SKU: " . $this->escaper->escapeHtml($product->getSku()) . PHP_EOL;
                $data .= "URL: " . $productUrl . PHP_EOL;

                $description = strip_tags((string)$product->getDescription());

                if (!empty($description)) {
                    $data .= "Description: " . $this->escaper->escapeHtml($description) . PHP_EOL;
                }

                $metaTitle = (string)$product->getMetaTitle();
                $metaDesc = (string)$product->getMetaDescription();

                if (!empty($metaTitle)) {
                    $data .= "Meta Title: " . $this->escaper->escapeHtml($metaTitle) . PHP_EOL;
                }
                if (!empty($metaDesc)) {
                    $data .= "Meta Description: " . $this->escaper->escapeHtml($metaDesc) . PHP_EOL;
                }
                $data .= "------------------------" . PHP_EOL;
            }

            $data .= PHP_EOL;

            $currentPage++; 
            $pageNumber++;
            $hasMore = ($collection->getSize() > ($pageSize * ($currentPage - 1)));
        }



        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToSelect(['name', 'url_key', 'description', 'meta_title', 'meta_description',]);
        $categoryCollection->addAttributeToFilter('is_active', 1);
        $categoryCollection->addAttributeToFilter('level', ['gt' => 1]); // Avoid root category

        $data .= PHP_EOL . "====== Product Categories and Products ======" . PHP_EOL;

        foreach ($categoryCollection as $category) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect(['name', 'sku', 'url_key']);
            $productCollection->addCategoryFilter($category);
            $productCollection->addAttributeToFilter('status', 1);
            $productCollection->addAttributeToFilter('visibility', ['neq' => 1]);
         
            $data .= 'Category Name: ' . $category->getName() . PHP_EOL;
            $data .= 'URL: ' . $baseUrl . $category->getUrlKey() . ".html" . PHP_EOL;
            $rawDescription = (string) $category->getDescription();

            // Remove <style> tags and their content
            $cleaned = preg_replace('#<style[^>]*>.*?</style>#is', '', $rawDescription);

            // Strip all remaining HTML tags
            $cleaned = strip_tags($cleaned);

            // Normalize spaces and remove line breaks
            $cleaned = preg_replace('/\s+/', ' ', $cleaned); // Remove newlines, tabs, multiple spaces
            $cleaned = trim($cleaned); // Final trim

            if (!empty($cleaned)) {
                $data .= 'Description: ' . $this->escaper->escapeHtml($cleaned) . PHP_EOL;
            }

            // Add Meta Title and Meta Description
            $metaTitle = $category->getMetaTitle();
            if (!empty($metaTitle)) {
                $data .= 'Meta Title: ' . $this->escaper->escapeHtml($metaTitle) . PHP_EOL;
            }

            $metaDescription = $category->getMetaDescription();
            if (!empty($metaDescription)) {
                $data .= 'Meta Description: ' . $this->escaper->escapeHtml($metaDescription) . PHP_EOL;
            }

                // if ($productCollection->getSize()) {
                //     foreach ($productCollection as $product) {
                //         $productUrl = $baseUrl . $product->getUrlKey() . ".html";
                //         $data .= '  - Product: ' . $product->getName() . PHP_EOL;
                //         $data .= '    SKU: ' . $product->getSku() . PHP_EOL;
                //         $data .= '    URL: ' . $productUrl . PHP_EOL;
                //     }
                // } else {
                //    // $data .= "  No active products found." . PHP_EOL;
                // }

            $data .= "------------------------" . PHP_EOL;
           
        }



        $pageCollection = $this->pageCollection;
        $pageCollection->addFieldToFilter('is_active', 1);
        $pageCollection->addFieldToSelect(['title', 'identifier', 'meta_title', 'meta_description']);

        $data .= PHP_EOL . "===== CMS PAGES =====" . PHP_EOL;
        foreach ($pageCollection as $cmsPage) {
            $cmsUrl = $baseUrl . $cmsPage->getIdentifier();
            if($cmsPage->getTitle() == "404 Not Found") continue;
            // Add .html suffix if not already present
            // if (substr($cmsUrl, -5) !== '.html') {
            //     $cmsUrl .= '.html';
            // }

            $data .= "Title: " . $this->escaper->escapeHtml($cmsPage->getTitle()) . PHP_EOL;
            $data .= "URL: " . $cmsUrl . PHP_EOL;
            if(!empty($cmsPage->getMetaTitle())) {
            $data .= "Meta Title: " . $this->escaper->escapeHtml((string)$cmsPage->getMetaTitle()) . PHP_EOL;
            }
            if (!empty($cmsPage->getMetaDescription())) {
            $data .= "Meta Description: " . $this->escaper->escapeHtml((string)$cmsPage->getMetaDescription()) . PHP_EOL;
            }
            $data .= "------------------------" . PHP_EOL;
        }

        $postCollectionFactory = $this->postCollectionFactory->create();
        $postCollectionFactory->addFieldToSelect(['name', 'url_key', 'post_content', 'meta_title', 'meta_description']);
        $postCollectionFactory->addFieldToFilter('status', 1); // ✅ Filter: status = 1
        $data .= "====== Blog =======";
        foreach ($postCollectionFactory as $item) {
            $data .= 'Title: ' . $item->getName() . PHP_EOL;
            $data .= 'URL: ' . $baseUrl."inedit/index/view/".$item->getUrlKey() . PHP_EOL;
            if ($item->getPostContent()) {
                $content = $item->getPostContent();

                // Remove <style> and <script> tags with content
                $content = preg_replace('#<style[^>]*>.*?</style>#is', '', $content);
                $content = preg_replace('#<script[^>]*>.*?</script>#is', '', $content);
                $content = preg_replace('/\{\{block\s+.*?\}\}/is', '', $content);
                // Remove all remaining HTML tags
                $content = strip_tags($content);

                // Remove extra whitespace, tabs, newlines
                $content = preg_replace('/\s+/', ' ', $content);
                $content = trim($content);

                if (!empty($content)) {
                    $data .= 'Content: ' . $this->escaper->escapeHtml($content) . PHP_EOL;
                }
            }

            if($item->getMetaTitle()) {
             $data .= 'Meta Title: ' . $item->getMetaTitle() . PHP_EOL;
             }
            if ($item->getMetaDescription()) {
                $data .= 'Meta Description: ' . $item->getMetaDescription() . PHP_EOL;
            }
            $data .= "------------------------" . PHP_EOL;
        }


        $brandCollectionFactory = $this->brandCollectionFactory->create();
        $brandCollectionFactory->addFieldToSelect(['name', 'url_key', 'description', 'page_title', 'meta_description']);
        $brandCollectionFactory->addFieldToFilter('status', 1); // ✅ Filter: status = 1
        $data .= "====== Brand =======";
        foreach ($brandCollectionFactory as $item) {
            $data .= 'Title: ' . $item->getName() . PHP_EOL;
            $data .= 'URL: ' . $baseUrl . "makers/" . $item->getUrlKey()."html" . PHP_EOL;

            if ($item->getDescription()) {
                $content = $item->getDescription();

                // Remove <style> and <script> tags with content
                $content = preg_replace('#<style[^>]*>.*?</style>#is', '', $content);
                $content = preg_replace('#<script[^>]*>.*?</script>#is', '', $content);
                $content = preg_replace('/\{\{block\s+.*?\}\}/is', '', $content);

                // Remove all remaining HTML tags
                $content = strip_tags($content);

                // Remove extra whitespace, tabs, newlines
                $content = preg_replace('/\s+/', ' ', $content);
                $content = trim($content);

                if (!empty($content)) {
                    $data .= 'Content: ' . $this->escaper->escapeHtml($content) . PHP_EOL;
                }
            }
           
            if ($item->getPageTitle()) {
                $data .= 'Meta Title: ' . $item->getPageTitle() . PHP_EOL;
            }
            if ($item->getMetaDescription()) {
                $data .= 'Meta Description: ' . $item->getMetaDescription() . PHP_EOL;
            }
            $data .= "------------------------" . PHP_EOL;
        }

        try {
            file_put_contents($filePath, '');
            file_put_contents($filePath, $data);
            $data = '';
            $message = "llms.txt file updated successfully at: " . $filePath;
        } catch (\Exception $e) {
            $this->logger->error('LLMS Controller Write Error: ' . $e->getMessage());
            $message = "Failed to write llms.txt file. Check logs.";
        }

        // Optionally also print on screen
      

        $result = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $result->setContents($message);
        return $result;
    }
}
