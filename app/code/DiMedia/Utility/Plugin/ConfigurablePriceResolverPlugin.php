<?php

namespace DiMedia\Utility\Plugin;

use Magento\ConfigurableProduct\Pricing\Price\ConfigurablePriceResolver;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigurablePriceResolverPlugin
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
    }

    public function aroundResolvePrice(
        ConfigurablePriceResolver $subject,
        callable $proceed,
        Product $product,
        $exclude = null
    ) {
        if ($product->getTypeId() !== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $proceed($product, $exclude);
        }

        $defaultPrice = $proceed($product, $exclude);

        $usedProducts = $product->getTypeInstance()->getUsedProducts($product);

        if (!empty($usedProducts)) {
            $firstChild = reset($usedProducts);
            $basePrice = (float)$firstChild->getFinalPrice();

            // ✅ Convert using Magento’s PriceCurrency service
            $convertedPrice = $this->priceCurrency->convert($basePrice, $this->storeManager->getStore());
            return $convertedPrice;
        }

        return $defaultPrice;
    }
}
