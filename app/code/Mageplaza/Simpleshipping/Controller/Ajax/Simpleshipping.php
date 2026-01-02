<?php

namespace Mageplaza\Simpleshipping\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Psr\Log\LoggerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Simpleshipping extends Action
{
    /**
     * @var DefaultConfigProvider
     */
    protected $defaultConfigProvider;
    protected $storeManager;
    protected $currencyFactory;
    protected $scopeConfig;
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var RateRequest
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Context $context
     * @param DefaultConfigProvider $defaultConfigProvider
     * @param Cart $cart
     * @param RateRequest $request
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        DefaultConfigProvider $defaultConfigProvider,
        Cart $cart,
        RateRequest $request,
        CheckoutSession $checkoutSession,
        CurrencyFactory $currencyFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->defaultConfigProvider = $defaultConfigProvider;
        $this->cart = $cart;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Execute method
     */
    public function execute()
    {
        // Get the current checkout configuration
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shippingAjax.log');
        $custom_logger = new \Zend_Log();
        $custom_logger->addWriter($writer);

        $result = $this->defaultConfigProvider->getConfig();
        $quote = $this->cart->getQuote();
        $store = $quote->getStore();
        $storeId = $store->getId();
        // if($storeId == 2) {
            $defaultCountrycode = "";
        // } else {
        //     $defaultCountrycode = "IN";
        // }

        $shippingAddress =  $quote->getShippingAddress();
        $countryCode = $shippingAddress->getCountryId();
        $currencyCode = "";
        // Perform the same operations as in your plugin
        $countryId =  (isset($_POST['country_code']) && !empty($_POST['country_code'])) ? $_POST['country_code'] : $defaultCountrycode;
        if( $countryId != "" ) {
            $currencyCode = $this->getCurrencyCodeByCountry($countryId);
            $currency = $this->currencyFactory->create()->load($currencyCode);
            $currencySymbol = $currency->getCurrencySymbol();

            $isRateSet = $this->isCurrencyRateSet($currencyCode);
            $currencyRate = $this->getCurrencyRate($currencyCode);
            $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
        }

        $custom_logger->info("post_country_code'-" . $_POST['country_code']);
        $custom_logger->info("session_country_code'-" . $countryCode); //to log the array
        $custom_logger->info("currencyCode'-" . $currencyCode); //to log the array
        



      
      
        $listItems = $quote->getAllItems();
        $strest = [];
        $i = 0;
        foreach ($listItems as $item) {
            $productId = $item->getProductId();
            $product = $item->getProduct();
            $quantity = $item->getQty();
            $product->load($productId);

            if (!$item->getParentItemId()) {
                if ($item->getProductType() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $productId = $item->getProductId();
                    $children = $item->getChildren();
                    if (!empty($children)) {
                        $childItem = reset($children);
                        $childProductId = $childItem->getProductId();
                        $parentProductId = $item->getProduct()->getId();
                    }
                } elseif ($item->getParentItemId()) {
                    $parentItem = $item->getParentItem();
                    $configurableProductId = $parentItem->getProductId();
                    $parentProductId = $configurableProductId;
                    $childProductId = $productId;
                } else {
                    $parentProductId = $productId;
                    $childProductId = $productId;
                }

                $product->load($childProductId);
                $shippingChargesFeatureEnabled = $product->getData('shipping_charges_feature_enabl');
                $weightPrice = $product->getData('weight_price');

                if ($shippingChargesFeatureEnabled == 1 && $weightPrice != "") {
                    $strest[] = $weightPrice * $quantity;
                } elseif ($shippingChargesFeatureEnabled == 0) {
                    $strest[] = "";
                } else {
                    $strest[] = " To Be Quoted";
                }

              

                // $this->logger->info("Child Product ID: " . $childProductId);
                // $this->logger->info("Custom Shipping Charge: " . $strest);

                // $result['quoteItemData'][$i]['customshippingcharge'] = $strest;

                $i++;
            }
        }

        // Return or update the result
        // You could return this as a JSON response, or update the session/quote, etc.
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode(["success" => true, "message" => $countryId,"order"=> $strest,"currencyCode"=> $currencyCode])
        );
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



// use Magento\Framework\App\Action\Action;
// use Magento\Framework\App\Action\Context;
// use Magento\Directory\Model\CurrencyFactory;
// use Magento\Framework\Controller\Result\JsonFactory;
// use Magento\Framework\App\Config\ScopeConfigInterface;

// class Simpleshipping extends Action
// {
//     protected $resultJsonFactory;
//     protected $currencyFactory;
//     protected $scopeConfig;

//     public function __construct(
//         Context $context,
//         JsonFactory $resultJsonFactory,
//         CurrencyFactory $currencyFactory,
//         ScopeConfigInterface $scopeConfig
//     ) {
//         $this->resultJsonFactory = $resultJsonFactory;
//         $this->currencyFactory = $currencyFactory;
//         $this->scopeConfig = $scopeConfig;
//         parent::__construct($context);
//     }

//     public function execute()
//     {
//         $countryId =  (isset($_POST['country_code']) && !empty($_POST['country_code'])) ? $_POST['country_code'] : "IN";
//         $result = $this->resultJsonFactory->create();
//         return $result->setData(["success" => true, "message" => $countryId]);
//    }

    
// }


