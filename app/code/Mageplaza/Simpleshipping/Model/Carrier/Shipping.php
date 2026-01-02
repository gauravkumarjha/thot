<?php

namespace Mageplaza\Simpleshipping\Model\Carrier;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'simpleshipping';
    protected $_checkoutSession;
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * Get shipping price
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');
        return $this->getFinalPriceWithHandlingFee($configPrice);
    }

    /**
     * Collect rates
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shipping_method_emailshipping.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode();
        
        $newPackageWeight = 0;
        $newPrice = 0;
        $shippingToBeQuoted = 0;
        $storeId = $request->getStoreId();
        $countryId =  $request->getDestCountryId();
        // $shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
        // $countryCode = $shippingAddress->getCountryId();
        // if($countryId!="") {
        //     $countryId = $countryId;
           
        // } else {
        //     $countryId = $countryCode;;
        // }
        // Log request parameters using print_r to convert array to string
        $logger->info('Request storeId: ' . $storeId);
        // $logger->info("getDestRegionCode-" . $shippingAddress->getCountryId()); //to log the array
        $logger->info("countryId-" . $countryId); //to log the array

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode();
        
        // if(empty($countryId)) {
        //     $countryId =  $request->getDestCountryId();
        // }
        // $logger->info("storeId-" . $storeId); //to log the array
        // $logger->info("countryId-". $countryId); //to log the array
        // $logger->info("countryIds-" .$request->getDestCountryId()); //to log the array
        foreach ($request->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                $itemWeight = $item->getWeight();
                if ($itemWeight == "") continue;

                $productId = $item->getProductId();
                $product = $item->getProduct();
                $logger->info("productId-" . $productId); //to log the array
                // Load product if necessary
            

                if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $children = $item->getChildren();
                    foreach ($children as $childItem) {
                        $childProductId = $childItem->getProductId();
                        $parentProductId = $item->getProduct()->getId();
                        $logger->info("childProductId-11--" . $childProductId); //to log the array
                        $childProduct = $childItem->getProduct();
                        $childProduct->load($childProductId);
                        $this->processProduct($childProduct, $item, $newPackageWeight, $newPrice, $shippingToBeQuoted);
                    }
                } elseif ($item->getParentItemId()) {
                    $parentItem = $item->getParentItem();
                    $configurableProductId = $parentItem->getProductId();
                    $parentProductId = $configurableProductId;
                    $childProductId = $productId;
                    if (!isset($childProductId) || $childProductId == "") continue;

                    $childProduct = $product;
                    $childProduct->load($childProductId);
                    $this->processProduct($childProduct, $item, $newPackageWeight, $newPrice, $shippingToBeQuoted);
                } else {
                    $parentProductId = $productId;
                    $childProductId = $productId;
                    $logger->info("childProductId-" . $childProductId); //to log the array
                    if (!isset($childProductId) || $childProductId == "") continue;

                    $childProduct = $product;
                    $childProduct->load($childProductId);
                    $this->processProduct($childProduct, $item, $newPackageWeight, $newPrice, $shippingToBeQuoted);
                }
             
               
            }
        }

        if ($newPackageWeight == 0) {
            $newPackageWeight = 0.1;
            $request->setPackageWeight($newPackageWeight);
        }
        $logger->info("childProductId-" . $newPackageWeight); //to log the array
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);

        $amount = $newPrice;
        $logger->info("countryId-" . $countryId); //to log the array
        if ($countryId === 'IN' && $storeId != 2 && $currencyCode=="INR") { 
            if ($shippingToBeQuoted === 0 && $amount > 0) {
                $method->setMethodTitle('Shipping ₹' . $amount);
            } elseif ($shippingToBeQuoted > 0) {
                $method->setMethodTitle('To Be Quoted (We will contact you within 48 hours)');
            } else {
                $method->setMethodTitle('Shipping free 1');
            }
        } else {
            $method->setMethodTitle('Shipping: To Be Quoted (We will contact you within 48 hours)');
        }

        if (($countryId != 'IN' || $storeId != 1) || $shippingToBeQuoted > 0) {
            $amount = 0;
        }
      
        $logger->info("amount-" . $newPackageWeight); //to log the array
        $method->setPrice($amount);
        $method->setCost($amount);
        $result->append($method);

        return $result;
    }

    /**
     * Process product to update weights and prices
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float &$newPackageWeight
     * @param float &$newPrice
     * @param int &$shippingToBeQuoted
     */
    private function processProduct($product, $item, &$newPackageWeight, &$newPrice, &$shippingToBeQuoted)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shipping_method_emailshipping.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
    

        $itemWeight = $item->getWeight();
        $shippingChargesFeatureEnabled = $product->getData('shipping_charges_feature_enabl');
        $weightPrice = $product->getData('weight_price');

        $logger->info("shippingChargesFeatureEnabled-".$shippingChargesFeatureEnabled); //to log the array
        $logger->info("weightPrice-" . $weightPrice); //to log the array

        if ($shippingChargesFeatureEnabled == "0" && ($weightPrice == null || $weightPrice == "")) {
            return;
        }

        if ($shippingChargesFeatureEnabled == "1" && ($weightPrice == null || $weightPrice == "")) {
            $shippingToBeQuoted++;
            return;
        }

        if ($itemWeight != 0.1) {
            $newPackageWeight += $itemWeight * $item->getQty();
            $newPrice += $weightPrice * $item->getQty();
            $logger->info("gaurav--" . $newPackageWeight ."-". $newPrice); //to log the array
        }
    }
}
