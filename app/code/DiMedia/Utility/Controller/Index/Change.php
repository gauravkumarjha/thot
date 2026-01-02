<?php

namespace DiMedia\Utility\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class Change extends Action
{
    protected $resultJsonFactory;
    protected $currencyFactory;
    protected $scopeConfig;
    protected $storeManager;


    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CurrencyFactory $currencyFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->currencyFactory = $currencyFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
     
        parent::__construct($context);
    }

    public function execute()
    {
        echo "gaurav";
        die();
        
        $result = $this->resultJsonFactory->create();
        $countryCode = $this->getRequest()->getParam('country_id');
        $countryCode = empty($countryCode) ? null : $countryCode;

        if ($countryCode !== null) {
            
            $currencyCode = $this->getCurrencyCodeByCountry($countryCode);
            $currency = $this->currencyFactory->create()->load($currencyCode);
            $currencySymbol = $currency->getCurrencySymbol();

            $isRateSet = $this->isCurrencyRateSet($currencyCode);
            $currencyRate = $this->getCurrencyRate($currencyCode);

            $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
            return $result->setData([
                'success' => true,
                'currency_symbol' => $currencySymbol,
                'currency_code' => $currencyCode,
                'rate_set' => $isRateSet,
                'rate' => $currencyRate
            ]);
        }

        // Return an error response if country code is not provided
      //  return $result->setData(['success' => false, 'error' => 'Country code is required']);
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
        return $currency->getAnyRate($baseCurrencyCode) ?: null; // Return null if no rate is set
    }
}
