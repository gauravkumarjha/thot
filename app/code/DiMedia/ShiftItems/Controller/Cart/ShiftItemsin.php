<?php

namespace DiMedia\ShiftItems\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CartModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

class ShiftItemsin extends Action
{
    protected $cart;
    protected $quoteFactory;
    protected $quoteRepository;
    protected $checkoutSession;
    protected $productRepository;
    protected $storeManager;
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
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    // public function execute()
    // {
    //     try {
    //         $currentQuote = $this->checkoutSession->getQuote();
    //         if (!$currentQuote->getId() || !$currentQuote->getItemsCount()) {
    //             throw new \Exception("No cart found in current store for the current user.");
    //         }

    //         $items = $currentQuote->getAllVisibleItems();

    //         // Set target store and currency
    //         $targetStoreId = 1; // change as per your target store ID
    //         $currencyCode = 'INR'; // target currency code

    //         $targetStore = $this->storeManager->getStore($targetStoreId);

    //         // Create new quote for target store
    //         $newQuote = $this->quoteFactory->create()->setStore($targetStore)->setStoreId($targetStoreId);

    //         // Load quote by customer if logged in
    //         if ($currentQuote->getCustomerId()) {
    //             $newQuote->loadByCustomer($currentQuote->getCustomerId());
    //         } else {
    //             // Guest user, new active quote
    //             $newQuote->setIsActive(true)
    //                 ->setCustomerIsGuest(true)
    //                 ->setQuoteId(null);
    //         }

    //         // Set currency properly on new quote
    //         $newQuote->setQuoteCurrencyCode($currencyCode);
    //         $newQuote->setBaseCurrencyCode($currencyCode);
    //         $newQuote->getStore()->setCurrentCurrencyCode($currencyCode);
    //         $newQuote->getCurrency()->setCurrencyCode($currencyCode);

    //         // Clear all existing items in new quote (if any)
    //         foreach ($newQuote->getAllItems() as $existingItem) {
    //             $newQuote->removeItem($existingItem->getId());
    //         }

    //         // Add items from current quote to new quote with correct prices for target store
    //         foreach ($items as $item) {
    //             if ($item->getProductType() === 'configurable') {
    //                 // Clone parent configurable item
    //                 $newParentItem = clone $item;
    //                 $newParentItem->setId(null)
    //                     ->setQuoteId($newQuote->getId())
    //                     ->setStoreId($targetStoreId);
    //                 $newQuote->addItem($newParentItem);

    //                 $simpleProductOption = $item->getOptionByCode('simple_product');
    //                 if ($simpleProductOption && $simpleProductOption->getProduct()) {
    //                     $simpleProductId = $simpleProductOption->getProduct()->getId();
    //                     $simpleProductForStore = $this->productRepository->getById($simpleProductId, false, $targetStoreId);
    //                     $childPrice = $simpleProductForStore->getPrice();

    //                     // Clone simple product child item
    //                     $newChildItem = clone $item;
    //                     $newChildItem->setId(null)
    //                         ->setQuoteId($newQuote->getId())
    //                         ->setProductId($simpleProductId)
    //                         ->setParentItemId($newParentItem->getId())
    //                         ->setName($simpleProductForStore->getName())
    //                         ->setQty($item->getQty())
    //                         ->setProductType($simpleProductForStore->getTypeId())
    //                         ->setBasePrice($childPrice)
    //                         ->setRowTotal($childPrice * $item->getQty())
    //                         ->setBaseRowTotal($childPrice * $item->getQty())
    //                         ->setPrice($childPrice)
    //                         ->setPriceInclTax($childPrice)
    //                         ->setBasePriceInclTax($childPrice)
    //                         ->setRowTotalInclTax($childPrice * $item->getQty())
    //                         ->setBaseRowTotalInclTax($childPrice * $item->getQty())
    //                         ->setCustomPrice($childPrice)
    //                         ->setOriginalCustomPrice($childPrice);

    //                     $newQuote->addItem($newChildItem);

    //                     // Set prices on parent item accordingly
    //                     $newParentItem->setPrice($childPrice)
    //                         ->setBasePrice($childPrice)
    //                         ->setRowTotal($childPrice * $item->getQty())
    //                         ->setBaseRowTotal($childPrice * $item->getQty())
    //                         ->setPriceInclTax($childPrice)
    //                         ->setBasePriceInclTax($childPrice)
    //                         ->setRowTotalInclTax($childPrice * $item->getQty())
    //                         ->setBaseRowTotalInclTax($childPrice * $item->getQty());
    //                 } else {
    //                     throw new \Exception("Simple product not found for configurable item ID: " . $item->getId());
    //                 }
    //             } else {
    //                 // Simple product or others
    //                 $newItem = clone $item;
    //                 $newItem->setId(null)
    //                     ->setQuoteId($newQuote->getId())
    //                     ->setStoreId($targetStoreId);

    //                 $simpleProductForStore = $this->productRepository->getById($newItem->getProductId(), false, $targetStoreId);
    //                 $childPrice = $simpleProductForStore->getPrice();
    //                 $qty = $item->getQty();

    //                 $newItem->setProductType($simpleProductForStore->getTypeId())
    //                     ->setBasePrice($childPrice)
    //                     ->setRowTotal($childPrice * $qty)
    //                     ->setBaseRowTotal($childPrice * $qty)
    //                     ->setPrice($childPrice)
    //                     ->setPriceInclTax($childPrice)
    //                     ->setBasePriceInclTax($childPrice)
    //                     ->setRowTotalInclTax($childPrice * $qty)
    //                     ->setBaseRowTotalInclTax($childPrice * $qty)
    //                     ->setCustomPrice($childPrice)
    //                     ->setOriginalCustomPrice($childPrice);

    //                 $newQuote->addItem($newItem);
    //             }
    //         }

    //         // Update quote totals once after adding all items
    //         $newQuote->setIsActive(true);
    //         $newQuote->setItemsCount(count($newQuote->getAllVisibleItems()));
    //         $newQuote->setItemsQty(array_sum(array_map(function ($item) {
    //             return $item->getQty();
    //         }, $newQuote->getAllVisibleItems())));

    //         $newQuote->collectTotals();

    //         // Save the quote properly via repository
    //         $this->quoteRepository->save($newQuote);

    //         // Set quote to checkout session
    //         $this->checkoutSession->setQuoteId($newQuote->getId());
    //         $this->checkoutSession->replaceQuote($newQuote);
    //         $this->checkoutSession->setCartWasUpdated(true);

    //         // Set cookie for store and currency
    //         $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
    //             ->setDurationOneYear()
    //             ->setPath('/')
    //             ->setHttpOnly(false);

    //         $this->cookieManager->setPublicCookie('store_code', $targetStore->getCode(), $metadata);
    //         $this->cookieManager->setPublicCookie('currency_code', $currencyCode, $metadata);

    //         $this->messageManager->addSuccessMessage(__('Items have been successfully shifted to the target store with currency %1.', $currencyCode));

    //         return $this->_redirect('checkout');
    //     } catch (LocalizedException $e) {
    //         $this->messageManager->addErrorMessage(__('An error occurred: ') . $e->getMessage());
    //     } catch (\Exception $e) {
    //         $this->messageManager->addErrorMessage(__('An error occurred while shifting items: ') . $e->getMessage());
    //     }
    // }

    public function execute()
    {
        try {
            $currentQuote = $this->checkoutSession->getQuote();
            if (!$currentQuote->getId() || !$currentQuote->getItemsCount()) {
                throw new \Exception("No cart found in Store 1 for the current user.");
            }
            $items = $currentQuote->getAllVisibleItems();

            $currencyCode = 'INR';
         
            $targetStoreId = 1; // Your target store ID
            $targetStore = $this->storeManager->getStore($targetStoreId);

            // Set current store and currency
            $targetStore->setCurrentCurrencyCode($currencyCode);
            $this->storeManager->setCurrentStore($targetStore->getCode());

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

            $this->cookieManager->setPublicCookie('store_code', "default", $metadata);
            $this->cookieManager->setPublicCookie('currency_code', $currencyCode, $metadata);

            $this->messageManager->addSuccessMessage(__('Items have been successfully shifted to the target store with INR currency.'));
            $this->_redirect('checkout');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('An error occurred: ') . $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while shifting items: ') . $e->getMessage());
        }
    }
}
