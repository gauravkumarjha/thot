<?php

namespace Mageplaza\Simpleshipping\Helper;

use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class CartTransfer extends AbstractHelper
{
    protected $cart;
    protected $productRepository;
    protected $storeManager;

    public function __construct(
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    public function switchStoreAndTransferCart($newStoreId)
    {
        $cartItems = $this->cart->getQuote()->getAllItems();
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/CartTransfer.log');
        $custom_logger = new \Zend_Log();
        $custom_logger->addWriter($writer);

        // Switch to the new store
        $this->storeManager->setCurrentStore($newStoreId);
        $custom_logger->info("Switched to newStoreId: " . $newStoreId);

        foreach ($cartItems as $item) {
            try {
                if ($item->getParentItemId()) {
                    // Skip child items; process only parent items
                    continue;
                }

                $productId = $item->getProductId();
                $product = $this->productRepository->getById($productId);
                $custom_logger->info("Processing product ID: " . $product->getId());

                $params = ['qty' => $item->getQty()];

                // Handle configurable products
                if ($product->getTypeId() === ConfigurableType::TYPE_CODE) {
                    $custom_logger->info("Configurable product detected for ID: " . $product->getId());

                    $children = $item->getChildren();
                    foreach ($children as $childItem) {
                        $childProductId = $childItem->getProductId();
                        $custom_logger->info("Child product ID: " . $childProductId);

                        $params['super_attribute'] = json_decode($childItem->getOptionByCode('attributes')->getValue(), true);
                    }
                }

                // Handle custom options
                if ($product->hasOptions()) {
                    $custom_logger->info("Custom options detected for product ID: " . $product->getId());
                    $params['options'] = $item->getOptions();
                }

                // Add product to cart
                $this->cart->addProduct($product, $params);
                $custom_logger->info("Product added successfully: " . $product->getName());
            } catch (\Exception $e) {
                $custom_logger->error("Error processing product ID " . $item->getProductId() . ": " . $e->getMessage());
            }
        }

        // Save the new cart
        try {
            $this->cart->save();
            $custom_logger->info("Cart transferred and saved successfully.");
        } catch (\Exception $e) {
            $custom_logger->error("Error saving cart: " . $e->getMessage());
        }
    }
}
