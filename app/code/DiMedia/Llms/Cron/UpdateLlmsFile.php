<?php

namespace DiMedia\Llms\Cron;

use Magento\Framework\App\State;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Escaper;
use Psr\Log\LoggerInterface;



use Magento\Cms\Model\ResourceModel\Page\Collection;
use Chetaru\Edit\Model\ResourceModel\Post\CollectionFactory as postCollectionFactory;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory as brandCollectionFactory;

class UpdateLlmsFile
{
    protected $productCollectionFactory;
    protected $storeManager;
    protected $state;
    protected $escaper;
    protected $logger;
    protected $pageCollection;
    protected $postCollectionFactory;
    protected $brandCollectionFactory;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        State $state,
        Escaper $escaper,
        postCollectionFactory $postCollectionFactory,
        brandCollectionFactory $brandCollectionFactory,
        Collection $Collection,
        LoggerInterface $logger
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->pageCollection = $Collection;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
    }

    public function execute()
    {
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


        $pageCollection = $this->pageCollection;
        $pageCollection->addFieldToFilter('is_active', 1);
        $pageCollection->addFieldToSelect(['title', 'identifier', 'meta_title', 'meta_description']);

        $data .= PHP_EOL . "===== CMS PAGES =====" . PHP_EOL;
        foreach ($pageCollection as $cmsPage) {
            $cmsUrl = $baseUrl . $cmsPage->getIdentifier();
            if ($cmsPage->getTitle() == "404 Not Found") continue;
            // Add .html suffix if not already present
            // if (substr($cmsUrl, -5) !== '.html') {
            //     $cmsUrl .= '.html';
            // }

            $data .= "Title: " . $this->escaper->escapeHtml($cmsPage->getTitle()) . PHP_EOL;
            $data .= "URL: " . $cmsUrl . PHP_EOL;
            $data .= "Meta Title: " . $this->escaper->escapeHtml((string)$cmsPage->getMetaTitle()) . PHP_EOL;
            $data .= "Meta Description: " . $this->escaper->escapeHtml((string)$cmsPage->getMetaDescription()) . PHP_EOL;
            $data .= "------------------------" . PHP_EOL;
        }

        $postCollectionFactory = $this->postCollectionFactory->create();
        $postCollectionFactory->addFieldToSelect(['name', 'url_key', 'post_content', 'meta_title', 'meta_description']);
        $postCollectionFactory->addFieldToFilter('status', 1); // ✅ Filter: status = 1
        $data .= "====== Blog =======";
        foreach ($postCollectionFactory as $item) {
            $data .= 'Title: ' . $item->getName() . PHP_EOL;
            $data .= 'URL: ' . $baseUrl . "/inedit/index/view/" . $item->getUrlKey() . PHP_EOL;
            if ($item->getPostContent()) {
                $data .= 'Content: ' . strip_tags($item->getPostContent()) . PHP_EOL;
            }
            if ($item->getMetaTitle()) {
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
            $data .= 'URL: ' . $baseUrl . "makers/" . $item->getUrlKey() . "html" . PHP_EOL;
            if ($item->getPostContent()) {
                $data .= 'Content: ' . strip_tags($item->getPostContent()) . PHP_EOL;
            }
            if ($item->getMetaTitle()) {
                $data .= 'Meta Title: ' . $item->getMetaTitle() . PHP_EOL;
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


        return $this;
    }
}
