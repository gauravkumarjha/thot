<?php
namespace Mageplaza\Simpleshipping\Plugin;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;

class UpdateCurrency
{
    protected $storeManager;
    protected $quoteRepository;

    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->storeManager = $storeManager;
        $this->quoteRepository = $quoteRepository;
    }

    public function aroundAssign(
        \Magento\Quote\Api\ShippingMethodManagementInterface $subject,
        callable $proceed,
        $cartId,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
    ) {
        // Proceed with the original method
        $result = $proceed($cartId, $shippingAssignment);

        // Get shipping address and country
        $quote = $shippingAssignment->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $countryId = $shippingAddress->getCountryId();

        // Logic to determine currency based on country
        $currencyMapping = [
            'US' => 'USD',
            'IN' => 'INR',
            'GB' => 'GBP',
        ];

        $currency = $this->getCurrencyCodeByCountry($countryId) ?? $this->storeManager->getStore()->getDefaultCurrencyCode();

        //$currency = $currencyMapping[$countryId] ?? $this->storeManager->getStore()->getDefaultCurrencyCode();

        // Update the quote currency
        if ($currency !== $quote->getQuoteCurrencyCode()) {
            $quote->setQuoteCurrencyCode($currency);
            $this->quoteRepository->save($quote);
        }

        return $result;
    }

    protected function getCurrencyCodeByCountry($countryCode)
    {
        // Define currency mapping based on country code
        switch ($countryCode) {
            case 'IN':
                return 'INR';
            case 'GB':
            case 'IO':
            case 'FK':
            case 'GS':
                return 'GBP'; // Use GBP for British Pound
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
                return 'EUR'; // Use EUR for Euro
            default:
                return 'USD'; // Default to USD if no mapping found
        }
    }
}
