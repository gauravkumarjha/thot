<?php

namespace DiMedia\CurrencySwitcher\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;

class Change extends Action
{
    protected $resultJsonFactory;
    protected $currencyFactory;
    protected $scopeConfig;
    protected $storeManager;
    protected $cart;
    protected $checkoutSession;
    protected $quoteRepository;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CurrencyFactory $currencyFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        Cart $cart
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->currencyFactory = $currencyFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->cart = $cart;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $countryCode = $this->getRequest()->getParam('country_id');
        $countryCode = empty($countryCode) ? null : $countryCode;

        if ($countryCode !== null) {

            $currencyCode = $this->getCurrencyCodeByCountry($countryCode);
            $currency = $this->currencyFactory->create()->load($currencyCode);
            $currencySymbol = $currency->getCurrencySymbol();

            $isRateSet = $this->isCurrencyRateSet($currencyCode);
            $currencyRate = $this->getCurrencyRate($currencyCode);

            // Set the store's currency code
            $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);

            // Update the session's quote with the new currency
            $quoteSession = $this->checkoutSession->getQuote();
            $quoteSession->setQuoteCurrencyCode($currencyCode)
                ->setTotalsCollectedFlag(false) // Reset totals collection flag
                ->collectTotals(); // Recalculate totals after currency change
            $this->quoteRepository->save($quoteSession);

            // Update the cart's quote with the new currency
            $quote = $this->cart->getQuote();
            $quote->setStore($this->storeManager->getStore())
                ->setCurrencyCode($currencyCode)
                ->setQuoteCurrencyCode($currencyCode) // Ensure quote currency is updated
                ->collectTotals(); // Recalculate totals
            $this->quoteRepository->save($quote);

            // Sync the session with the updated quote
            $this->checkoutSession->replaceQuote($quote);

            return $result->setData([
                'success' => true,
                'currency_symbol' => $currencySymbol,
                'currency_code' => $currencyCode,
                'rate_set' => $isRateSet,
                'rate' => $currencyRate,
            ]);
        }

        return $result->setData(['success' => false, 'error' => 'Country code is required']);
    }

    protected function getCurrencyCodeByCountry($countryCode)
    {
        switch ($countryCode) {
            case 'IN':
                return 'INR';
            case 'GB':
            case 'IO':
            case 'FK':
            case 'GS':
                return 'GBP';
            case 'AE':
                return 'AED';
            case 'AX':
            case 'AD':
            case 'AT':
            case 'BE':
            case 'BQ':
            case 'HR':
            case 'CY':
            case 'EE':
            case 'FI':
            case 'FR':
            case 'GF':
            case 'TF':
            case 'DE':
            case 'GR':
            case 'GP':
            case 'IE':
            case 'IT':
            case 'XK':
            case 'LV':
            case 'LU':
            case 'MT':
            case 'MQ':
            case 'YT':
            case 'MC':
            case 'ME':
            case 'NL':
            case 'PT':
            case 'RE':
            case 'SM':
            case 'ST':
            case 'SK':
            case 'SI':
            case 'ES':
            case 'BL':
            case 'VA':
                return 'EUR';
            default:
                return 'USD'; // Default to USD
        }
    }

    protected function isCurrencyRateSet($currencyCode)
    {
        $baseCurrencyCode = $this->scopeConfig->getValue('currency/options/base', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $currency = $this->currencyFactory->create()->load($currencyCode);
        return $currency->getAnyRate($baseCurrencyCode) !== false;
    }

    protected function getCurrencyRate($currencyCode)
    {
        $baseCurrencyCode = $this->scopeConfig->getValue('currency/options/base', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $currency = $this->currencyFactory->create()->load($currencyCode);
        return $currency->getAnyRate($baseCurrencyCode) ?: null;
    }
}
