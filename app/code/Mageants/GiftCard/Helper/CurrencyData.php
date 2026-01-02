<?php
/**
 * @category Mageants GiftCard
 * @package Mageants_GiftCard
 * @copyright Copyright (c) 2016  Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\GiftCard\Helper;

use Magento\Checkout\Model\CartFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Helper CurrencyData
 */
class CurrencyData
{
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var Magento\Checkout\Model\CartFactory
     */
    protected $cartFactory;

    public const NO_SYMBOL = 1;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     * @param CartFactory $cartFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        CartFactory $cartFactory
    ) {
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->cartFactory = $cartFactory;
    }

    /**
     * Get base currency code
     *
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->storeManager->getStore()->getBaseCurrency()->getCurrencyCode();
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public function getCurrentCurrency()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCurrencyCode();
    }

    /**
     * Get currency factory
     *
     * @return CurrencyFactory
     */
    public function getCurrencyFactory()
    {
        return $this->currencyFactory->create();
    }

    /**
     * Get cart items
     *
     * @return CartFactory
     */
    public function getCartItems()
    {
        return $this->cartFactory->create()->getQuote()->getAllItems();
    }
}
