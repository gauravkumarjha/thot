<?php

namespace DiMedia\ShiftItems\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CartModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Bootstrap;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

class ShiftItems extends Action
{
    protected $cart;
    protected $quoteFactory;
    protected $storeManager;
    protected $quoteRepository;
    protected $checkoutSession;
    protected $productRepository;
    protected $cookieManager;
    protected $cookieMetadataFactory;

    public function __construct(
        Context $context,
        CartModel $cart,
        QuoteFactory $quoteFactory,
        QuoteRepository $quoteRepository,
        CheckoutSession $checkoutSession,
         ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        parent::__construct($context);
        $this->cart = $cart;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    //     public function execute()
    //     {
    //         try {
    //             // Get current store's cart
    //             // $currentCart = $this->cart;
    //             // $items = $currentCart->getQuote()->getAllVisibleItems(); // Get visible items from the current cart
    //             echo "gaurav";
    //             $currentQuote = $this->checkoutSession->getQuote();
    //             if (!$currentQuote->getId() || !$currentQuote->getItemsCount()) {
    //                 throw new \Exception("No cart found in Store 1 for the current user.");
    //             }
    //             echo "gaurav";
    //             $items = $currentQuote->getAllVisibleItems();

    //             echo "gaurav";

    //             $currencyCode = 'USD';
    //             // Set target store ID
    //             $targetStoreId = 3; // Replace with your target store ID
    //             $store2Id = 3; // Store 2
    //             $targetStore = $this->storeManager->getStore($targetStoreId);
    //             if ($targetStore) {
    //                 $targetStore->setCurrentCurrencyCode($currencyCode);
    //                 $this->storeManager->setCurrentStore($targetStore);
    //             }
    //             $newQuote = $this->quoteFactory->create()->setStoreId($store2Id);

    //             if ($currentQuote->getCustomerId()) {
    //                 $newQuote->loadByCustomer($currentQuote->getCustomerId());
    //             } else {
    //                 // Guest user, new active quote
    //                 $newQuote->setIsActive(true)
    //                     ->setCustomerIsGuest(true)
    //                     ->setQuoteId(null);
    //             }

    //             $newQuote->setQuoteCurrencyCode($currencyCode);
    //             $newQuote->setBaseCurrencyCode($currencyCode);
    //             $newQuote->getStore()->setCurrentCurrencyCode($currencyCode);
    //             $newQuote->getCurrency()->setCurrencyCode($currencyCode);

    //             // Add items from Store 1's quote to Store 2's quote
    //             foreach ($items as $item) {
    //                 if ($item->getProductType() === 'configurable') {

    //                     // Clone the parent item (configurable product)
    //                     $newParentItem = clone $item;
    //                     $newParentItem->setId(null); // Reset ID for the new quote
    //                     $newParentItem->setQuoteId($newQuote->getId());

    //                     // Save the parent item first to get its ID
    //                     $newQuote->addItem($newParentItem);
    //                     $newQuote->setIsActive(true);
    //                     $newQuote->collectTotals()->save(); // Ensure the item is persisted and totals are updated

    //                     // Get the saved parent item's ID
    //                     $newParentItemId = $newParentItem->getId();
    //                     if (!$newParentItemId) {
    //                         throw new \Exception("Failed to save parent item for configurable product.");
    //                     }

    //                     // Retrieve the associated simple product (child product)
    //                     $simpleProduct = $item->getOptionByCode('simple_product');
    //                     $parentPrice = 0;
    //                     if ($simpleProduct && $simpleProduct->getProduct()) {
    //                         $simpleProductId = $simpleProduct->getProduct()->getId();
    //                         $simpleProductForStore = $this->productRepository->getById($simpleProductId, false, $targetStoreId);

    //                         if ($simpleProductId) {
    //                             // Clone and modify the item to represent the child product
    //                             $newChildItem = clone $item;
    //                             $newChildItem->setId(null); // Reset ID for the new quote
    //                             $newChildItem->setQuoteId($newQuote->getId());
    //                             $newChildItem->setProductId($simpleProductId); // Use the child product ID
    //                             $newChildItem->setParentItemId($newParentItemId); // Set parent item ID
    //                             $newChildItem->setName($simpleProductForStore->getName());
    //                             $newChildItem->setQty(1);
    //                             $newChildItem->setProductType($simpleProductForStore->getTypeId());
    //                             $childPrice = $simpleProductForStore->getPrice();
    //                             $parentPrice += $childPrice * $newChildItem->getQty();


    //                             $newChildItem->setBasePrice($childPrice);
    //                             $newChildItem->setRowTotal($parentPrice);
    //                             $newChildItem->setBaseRowTotal($parentPrice);
    //                             $newChildItem->setPrice($childPrice);
    //                             $newChildItem->setPriceInclTax($childPrice);
    //                             $newChildItem->setBasePriceInclTax($childPrice);
    //                             $newChildItem->setRowTotalInclTax($parentPrice);
    //                             $newChildItem->setBaseRowTotalInclTax($parentPrice);
    //                             $newQuote->addItem($newChildItem);

    //                             // Update the price for Store 2
    //                             $newChildItem->setBasePrice($simpleProduct->getProduct()->getPrice());
    //                             $newChildItem->setCustomPrice($simpleProduct->getProduct()->getPrice());
    //                             $newChildItem->setOriginalCustomPrice($simpleProduct->getProduct()->getPrice());
    //                             $newChildItem->setPrice($simpleProduct->getProduct()->getPrice());
    //                         } else {
    //                             throw new \Exception("Simple product ID not found for configurable item ID: " . $item->getId());
    //                         }
    //                     } else {
    //                         throw new \Exception("Could not retrieve the simple product for configurable item ID: " . $item->getId());
    //                     }
    //                     $newParentItem->setPrice($childPrice);
    //                     $newParentItem->setBasePrice($childPrice);
    //                     $newParentItem->setRowTotal($parentPrice);
    //                     $newParentItem->setBaseRowTotal($parentPrice);

    //                     $newParentItem->setPriceInclTax($childPrice);
    //                     $newParentItem->setBasePriceInclTax($childPrice);
    //                     $newParentItem->setRowTotalInclTax($parentPrice);
    //                     $newParentItem->setBaseRowTotalInclTax($parentPrice);

    //                     // Save the updated parent item
    //                     //$newQuote->collectTotals()->save();


    //                 } else {
    //                     // For simple products or other types, transfer as-is
    //                     $parentPrice = 0;
    //                     $newItem = clone $item; 
    // $newItem->setId(null); // Reset ID for the new quote
    // $newItem->setQuoteId($newQuote->getId());

    // $simpleProductForStore = $this->productRepository->getById($newItem->getProductId(), false, $targetStoreId);
    // $childPrice = $simpleProductForStore->getPrice();
    // $newItem->setProductType($simpleProductForStore->getTypeId());

    // $newItem->setBasePrice($childPrice);
    // $newItem->setRowTotal($childPrice * $newItem->getQty());
    // $newItem->setPrice($childPrice);
    // $newItem->setPriceInclTax($childPrice);
    // $newItem->setBasePriceInclTax($childPrice);
    // $newItem->setRowTotalInclTax($childPrice * $newItem->getQty());
    // $newItem->setBaseRowTotal($childPrice * $newItem->getQty());
    // $newItem->setBaseRowTotalInclTax($childPrice * $newItem->getQty());


    //                     // Update the price for Store 2
    //                      $newItem->setBasePrice($item->getPrice());
    //                     $newItem->setCustomPrice($item->getPrice());
    //                     $newItem->setOriginalCustomPrice($item->getPrice());
    //                     $newItem->setPrice($item->getPrice());

    //                     // $newItem->setBasePrice($item->getPrice());
    //                     // $newItem->setCustomPrice($item->getPrice());
    //                     // $newItem->setOriginalCustomPrice($item->getPrice());
    //                     // $newItem->setPrice($item->getPrice());

    //                     $newQuote->addItem($newItem);
    //                 }
    //             }

    //             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    //             $newQuote->setIsActive(true);

    //             $newQuote->setStoreId($targetStoreId);

    //             $newQuote->setItemsCount(count($newQuote->getAllVisibleItems()));
    //             $newQuote->setItemsQty(array_sum(array_map(function ($item) {
    //                 return $item->getQty();
    //             }, $newQuote->getAllVisibleItems())));

    //             $quoteResource = $objectManager->get(\Magento\Quote\Model\ResourceModel\Quote::class);
    //             $quoteResource->save($newQuote);
    //             $newQuote->collectTotals();

    //             // Save the quote properly via repository
    //             $this->quoteRepository->save($newQuote);

    //             // Set quote to checkout session

    //             $this->checkoutSession->setQuoteId($newQuote->getId());
    //             $this->checkoutSession->replaceQuote($newQuote);
    //             $this->checkoutSession->setCartWasUpdated(true);

    //             $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
    //             $logger->info('Subtotal: ' . $newQuote->getSubtotal());
    //             $logger->info('Grand Total: ' . $newQuote->getGrandTotal());

    //             // $cartRepository = $objectManager->get('Magento\Quote\Api\CartRepositoryInterface');
    //             // $cartRepository->save($newQuote); // Saving the quote   
    //             // Set the quote as active, recalculate totals, and save


    //             # $newQuote->collectTotals()->save();
    //             $metadata = $this->cookieMetadataFactory
    //                 ->createPublicCookieMetadata()
    //                 ->setDurationOneYear()
    //                 ->setPath('/')
    //                 ->setHttpOnly(false);

    //             $this->cookieManager->setPublicCookie(
    //                 'store_code',
    //                 "usd",
    //                 $metadata
    //             );

    //             // Optional: Save currency code as well
    //             $this->cookieManager->setPublicCookie(
    //                 'currency_code',
    //                 $currencyCode,
    //                 $metadata
    //             );
    //             // Collect totals and save the target quote

    //             // Redirect to success or desired page
    //             $this->messageManager->addSuccessMessage(__('Items have been successfully shifted to the target store with USD currency.'));
    //             $this->_redirect('checkout');
    //         } catch (LocalizedException $e) {
    //             // Handle Magento-specific exceptions
    //             $this->messageManager->addErrorMessage(__('An error occurred: ') . $e->getMessage());
    //         } catch (\Exception $e) {
    //             // Handle general exceptions
    //             $this->messageManager->addErrorMessage(__('An error occurred while shifting items: ') . $e->getMessage());
    //         }
    //     }
    public function execute()
    {
        try {
            $currentQuote = $this->checkoutSession->getQuote();
            if (!$currentQuote->getId() || !$currentQuote->getItemsCount()) {
                throw new \Exception("No cart found in Store 1 for the current user.");
            }
            $items = $currentQuote->getAllVisibleItems();

            $currencyCode = 'USD';
            $targetStoreId = 3; // Your target store ID
            $targetStore = $this->storeManager->getStore($targetStoreId);

            // Set current store and currency
            $targetStore->setCurrentCurrencyCode($currencyCode);
            $this->storeManager->setCurrentStore($targetStore);

            // Load or create new quote for target store
            $newQuote = $this->quoteFactory->create()->setStoreId($targetStoreId);
            if ($currentQuote->getCustomerId()) {
                $newQuote->loadByCustomer($currentQuote->getCustomerId());
            } else {
                $newQuote->setIsActive(true)
                    ->setCustomerIsGuest(true)
                    ->setQuoteId(null);
            }
               foreach ($newQuote->getAllItems() as $existingItem) {
                $newQuote->removeItem($existingItem->getId());
            }
            $newQuote->setQuoteCurrencyCode($currencyCode);
            $newQuote->setBaseCurrencyCode($currencyCode);

            // Use cart model for the target store to add products properly
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cart = $objectManager->create(\Magento\Checkout\Model\Cart::class);
            $cart->setQuote($newQuote);

            foreach ($items as $item) {
                $productId = $item->getProductId();
                $qty = $item->getQty();

                $product = $this->productRepository->getById($productId, false, $targetStoreId);

                // For configurable products, add the child simple product with options
                if ($item->getProductType() === 'configurable') {
                    $simpleProductOption = $item->getOptionByCode('simple_product');
                    if ($simpleProductOption && $simpleProductOption->getProduct()) {
                        $simpleProduct = $simpleProductOption->getProduct();
                        $buyRequest = $item->getBuyRequest();

                        // addProduct with buyRequest includes configurable options
                        $cart->addProduct($product, $buyRequest);
                    } else {
                        throw new \Exception("Simple product option not found for configurable product ID: " . $productId);
                    }
                } else {
                    // For simple and other product types, add product with quantity
                    $cart->addProduct($product, ['qty' => $qty]);
                }
            }

            // Save cart and quote
            $cart->save();

            $newQuote = $cart->getQuote();

            $newQuote->setIsActive(true);
            $newQuote->setStoreId($targetStoreId);

            $this->quoteRepository->save($newQuote);

            $this->checkoutSession->setQuoteId($newQuote->getId());
            $this->checkoutSession->replaceQuote($newQuote);
            $this->checkoutSession->setCartWasUpdated(true);

            $metadata = $this->cookieMetadataFactory
                ->createPublicCookieMetadata()
                ->setDurationOneYear()
                ->setPath('/')
                ->setHttpOnly(false);

            $this->cookieManager->setPublicCookie('store_code', $targetStore->getCode(), $metadata);
            $this->cookieManager->setPublicCookie('currency_code', $currencyCode, $metadata);

            $this->messageManager->addSuccessMessage(__('Items have been successfully shifted to the target store with USD currency.'));
            $this->_redirect('checkout');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('An error occurred: ') . $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while shifting items: ') . $e->getMessage());
        }
    }
}
