<?php

namespace DiMedia\SendEmail\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductSaveAfterObserver implements ObserverInterface
{
    protected $transportBuilder;
    protected $storeManager;
    protected $logger;
    protected $productRepository;

    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();
        $bypassAttributes = ["required_options", "has_options", "image_label", "small_image_label", "thumbnail_label"];
        $sendEmail = false; // Initialize variable
        $store = $this->storeManager->getStore();
        // Iterate over each attribute to check "Use Default" status
        foreach ($product->getAttributes() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $isUsingDefault = $this->isUseDefault($productId, $attributeCode);
            
            // Log whether "Use Default" is checked or unchecked
            if ($isUsingDefault) {
                $this->logger->info("The 'Use Default' option is checked for attribute '{$attributeCode}'.");
            } else {
                $this->logger->info("The 'Use Default' option is unchecked for attribute '{$attributeCode}'.");
                // Mark for email if it's not a bypass attribute and "Use Default" is unchecked
                // if (!in_array($attributeCode, $bypassAttributes)) {
                $sendEmail = true;
                //}
            }
        }

        // Send email if "Use Default" was unchecked for any relevant attribute
        if ($sendEmail) {
            try {
                $templateVars = [
                    'product_name' => $product->getName() ?? 'Default Name',
                    'product_sku' => $product->getSku() ?? 'Default SKU'
                ];

                $this->sendEmail($templateVars);
                $this->logger->info("Email sent successfully for product '{$product->getSku()}'.");
            } catch (\Exception $e) {
                $this->logger->error('Error sending email: ' . $e->getMessage());
            }
        }
    }

    private function sendEmail($templateVars)
    {
        $store = $this->storeManager->getStore();
        $this->logger->info(json_encode($templateVars));
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('custom_product_save_template_sandeep') // Template ID
            ->setTemplateOptions([
                'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                'store' => $store->getId()
            ])
            ->setTemplateVars($templateVars)
            ->setFrom(['email' => "gaurav@digitalimpressions.in", 'name' => "Test"]) // From email
            ->addTo('gaurav@digitalimpressions.in') // Recipient
            ->getTransport();

        $transport->sendMessage();
        $this->logger->info("Email sent successfully.");
    }

    public function isUseDefault($productId, $attributeCode)
    {
        $store = $this->storeManager->getStore();
        $this->logger->info("store-".$store->getId());
        // Load product in default and specific store views
        $productDefault = $this->productRepository->getById($productId, false, 0);
        $productStore1 = $this->productRepository->getById($productId, false, $store->getId());

        // Compare attribute values between default and specific store
        $defaultValue = $productDefault->getData($attributeCode);
        $storeValue = $productStore1->getData($attributeCode);

        // Check if store-specific value is different from default
        return $storeValue === $defaultValue;
    }
}
