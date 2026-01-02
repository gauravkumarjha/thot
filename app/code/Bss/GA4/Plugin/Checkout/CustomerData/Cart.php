<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * @category   BSS
 * @package    Bss_GA4
 * @author     Extension Team
 * @copyright  Copyright (c) 2022-2023 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GA4\Plugin\Checkout\CustomerData;

use Bss\GA4\Model\DataItem;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Add Bss data to customer section
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Cart
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Quote\Model\Quote|null
     */
    protected $quote = null;

    /**
     * @var DataItem
     */
    protected $dataItem;

    /**
     * @var \Bss\GA4\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Session $checkoutSession
     * @param Data $checkoutHelper
     * @param DataItem $dataItem
     * @param \Bss\GA4\Helper\Data $dataHelper
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Bss\GA4\Model\DataItem $dataItem,
        \Bss\GA4\Helper\Data $dataHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
        $this->dataItem = $dataItem;
        $this->dataHelper = $dataHelper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Add data to result
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        $checkoutRemove = $this->checkoutSession->getRemoveItem();
        $wishListFromCart = $this->checkoutSession->getWishListFromCart();
        $dataSectionBss = $addWishListFromCart = '';
        if ($checkoutRemove && $checkoutRemove['removeItem']) {
            $productId = $checkoutRemove['productId'];
            $price = $checkoutRemove['price'];
            $info = [
                $productId => $checkoutRemove['qty']
            ];
            $dataSectionBss = $this->addSectionDataBss($info, "remove_from_cart", $price);
            if ($wishListFromCart && $wishListFromCart['addToWishListFromCart']) {
                $addWishListFromCart = $this->addSectionDataBss($info, 'add_to_wishlist', $price);
                $this->checkoutSession->unsWishListFromCart();
            }
            $this->checkoutSession->unsRemoveItem();
        }
        $checkoutAddCart =  $this->checkoutSession->getProductAddToCart();
        if (isset($checkoutAddCart) && $checkoutAddCart['isAddToCart'] && !isset($checkoutAddCart['info'])) {
            $productId = $checkoutAddCart['productId'];
            $qty = 1;
            if ($checkoutAddCart['qty']) {
                $qty = $checkoutAddCart['qty'];
            }
            $price = $checkoutAddCart['price'];
            $info = [
                $productId => $qty
            ];
            $dataSectionBss = $this->addSectionDataBss($info, "add_to_cart", $price);
            $this->checkoutSession->unsProductAddToCart();
        }

        if (isset($checkoutAddCart) && isset($checkoutAddCart['info'])) {
            $dataSectionBss = $this->addSectionDataBss($checkoutAddCart['info'], "add_to_cart");
            $this->checkoutSession->unsProductAddToCart();
        }
        if ($dataSectionBss) {
            $result['bss_ga4'] = $dataSectionBss;
        }
        if ($addWishListFromCart) {
            $result['bss_ga4_wishlist_from_cart'] = $addWishListFromCart;
        }
        $result['currency'] = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        return $result;
    }

    /**
     * Add section data
     *
     * @param array $info
     * @param string $event
     * @param float|null $price
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function addSectionDataBss($info, $event, $price = null)
    {
        $checkoutAddCart =  $this->checkoutSession->getProductAddToCart();
        $storeId = $this->storeManager->getStore()->getId();
        $prepareItem = [];
        $totalFinalPrice = 0;
        $index = 0;
        foreach ($info as $productId => $qty) {
            ++$index;
            if ($qty > 0) {
                $product = $this->productRepository->getById($productId, false, (string)$storeId);
                $totalFinalPrice += $product->getFinalPrice() * $qty;
                $item = $this->dataItem->renderItem($product);
                $item['quantity'] = $qty;
                if ($product->getTierPrice()) {
                    $item['price'] = $this->dataHelper->convertPriceCurrency($product->getFinalPrice($qty));
                }
                $item['index'] = $index;
                if (isset($checkoutAddCart['variant'])) {
                    $item['item_variant'] = $checkoutAddCart['variant'];
                }
                if (isset($checkoutAddCart['item_list_id'])) {
                    $item['item_list_id'] = $checkoutAddCart['item_list_id'];
                }
                if (isset($checkoutAddCart['item_list_name'])) {
                    $item['item_list_name'] = $checkoutAddCart['item_list_name'];
                }
                $prepareItem[] = $item;
            }
        }
        if (!$price) {
            $price = $totalFinalPrice;
        }
        $dataItems = [
            "currency" => $this->storeManager->getStore()->getCurrentCurrencyCode(),
            "value" => $price,
            "items" => $prepareItem
        ];
        $data = [
            "event",
            $event,
            $dataItems
        ];
        return $this->dataHelper->serializeItem($data);
    }
}
