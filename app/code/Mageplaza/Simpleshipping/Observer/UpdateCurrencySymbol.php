<?php

namespace Vendor\Module\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Cache\TypeListInterface;

class UpdateCurrencySymbol implements ObserverInterface
{
    protected $currencyFactory;
    protected $cacheTypeList;

    public function __construct(
        CurrencyFactory $currencyFactory,
        TypeListInterface $cacheTypeList
    ) {
        $this->currencyFactory = $currencyFactory;
        $this->cacheTypeList = $cacheTypeList;
    }

    public function execute(Observer $observer)
    {
        // Logic to update the currency symbol
        $shippingAddress = $observer->getEvent()->getQuote()->getShippingAddress();
        $countryId = $shippingAddress->getCountryId();

        // Map country to currency
        $currencyMap = [
            'US' => 'USD',
            'IN' => 'INR',
            // Add more mappings as needed
        ];

        if (isset($this->getCurrencyCodeByCountry[$countryId])) {
            $currencyCode = $$this->getCurrencyCodeByCountry[$countryId];

            // Update currency symbol in session
            $currency = $this->currencyFactory->create()->load($currencyCode);
            $currency->setCurrencySymbolOverride($currency->getCurrencySymbol());

            // Clear cache to reflect changes
            $this->cacheTypeList->cleanType('config');
        }
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
