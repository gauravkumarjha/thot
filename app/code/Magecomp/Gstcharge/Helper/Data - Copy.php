<?php
namespace Magecomp\Gstcharge\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product as ProductData;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Backend\Model\Session\Quote;
use Magento\Directory\Model\Region;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Model\ProductRepository as ProductDataByRepository;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class Data extends AbstractHelper
{
    const CONFIG_CUSTOM_IS_ENABLED = 'Gstcharge/Gstcharge/status';
    const CONFIG_QR_ENABLED = 'Gstcharge/Gstcharge/qrstatus';
    const CONFIG_GST_TAXPER = 'Gstcharge/Gstcharge/tax_percent';
    const CONFIG_GST_TAXPER_MIN_PRICE = 'Gstcharge/Gstcharge/tax_percent_minprice';
    const CONFIG_GST_MIN_PRICE = 'Gstcharge/Gstcharge/tax_minprice';
    const CONFIG_GST_STATE = 'Gstcharge/Gstcharge/state';
    const CONFIG_GST_NUMBER = 'Gstcharge/Gstcharge/gstnumber';
    const CONFIG_PAN_NUMBER = 'Gstcharge/Gstcharge/pannumber';
    const CONFIG_CIN_NUMBER = 'Gstcharge/Gstcharge/cinnumber';
    const CONFIG_GST_TAXTYPE = 'Gstcharge/Gstcharge/taxtype';
    const CONFIG_DISCOUNT_GST_TAXTYPE = 'tax/calculation/discount_tax';
    const CONFIG_GST_SIGNATURE = 'Gstcharge/Gstcharge/authentication';
    const CONFIG_GST_SIGNATURETEXT = 'Gstcharge/Gstcharge/signaturetext';
    const CONFIG_GST_ONSHIPPING = 'Gstcharge/ShippingGstchargeConfig/shippingchargeinclude';
    const CONFIG_GST_SHIPPING_TAXTYPE = 'Gstcharge/ShippingGstchargeConfig/taxtype';
    const CONFIG_GST_BUYERGST = 'Gstcharge/Gstcharge/buyergst';
    const CONFIG_GST_NUMBER_REQUIRED = 'Gstcharge/Gstcharge/buyergstrequired';


    protected $_productloader;
    protected $_checkoutSession;
    protected $backendQuoteSession;
    protected $productdata;
    protected $regiondata;
    protected $httpContext;
    protected $customer;
    protected $storeManager;
    protected $productdatabyrepository;
    protected $_categoryFactory;
    protected $quoteRepository;
    protected $file;
    protected $newDirectory;

    public function __construct(
        Context $context,
        ProductFactory $_productloader,
        CategoryFactory $_categoryFactory,
        CheckoutSession $checkoutSession,
        Quote $backendQuoteSession,
        CartRepositoryInterface $quoteRepository,
        ProductData $productdata,
        Region $regiondata,
        ProductDataByRepository $productdatabyrepository,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customer,
        Filesystem $file
    ) {
        $this->_categoryFactory = $_categoryFactory;
        $this->_productloader = $_productloader;
        $this->_checkoutSession = $checkoutSession;
        $this->backendQuoteSession = $backendQuoteSession;
        $this->quoteRepository = $quoteRepository;
        $this->productdata = $productdata;
        $this->regiondata = $regiondata;
        $this->httpContext = $httpContext;
        $this->customer = $customer;
        $this->productdatabyrepository = $productdatabyrepository;
        $this->newDirectory = $file->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context);
    }

    public function createDirectory()
    {
        $logPath = "magecomp";
        $newDirectory = false;
        try {
            $newDirectory = $this->newDirectory->create($logPath);
        } catch (FileSystemException $e) {
            throw new LocalizedException(
                __('We can\'t create directory "%1"', $logPath)
            );
        }

        return $newDirectory;
    }

    public function getCustomer()
    {
            $customer = $this->customer;
            return $customer;
    }
   
    public function isLoggedIn()
    {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        return $isLoggedIn;
    }

    public function getStoreid()
    {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
         $storeId=$storeManager->getStore()->getId();
         return $storeId;
    }

    public function isModuleEnabled()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope, $this->getStoreid());

        return $isEnabled;
    }
    
    public function isModuleEnabledDisable($storeId)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope, $storeId);

        return $isEnabled;
    }

    public function isQrEnabled()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $isQrEnabled = $this->scopeConfig->getValue(self::CONFIG_QR_ENABLED, $storeScope, $this->getStoreid());
        return $isQrEnabled;
    }
    public function isGstApplyOnShpping()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $isShipping = $this->scopeConfig->getValue(self::CONFIG_GST_ONSHIPPING, $storeScope, $this->getStoreid());
        return $isShipping;
    }

    public function getGstTaxperConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_GST_TAXPER, $storeScope, $this->getStoreid());
        return $fee;
    }

    public function getGstTaxperConfigStore($storeId)
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_GST_TAXPER, $storeScope, $storeId);
        return $fee;
    }

    public function getGstTaxMinPriceConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_GST_MIN_PRICE, $storeScope, $this->getStoreid());
        return $fee;
    }
    public function getGstTaxPerMinPriceConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $fee = $this->scopeConfig->getValue(self::CONFIG_GST_TAXPER_MIN_PRICE, $storeScope, $this->getStoreid());
        return $fee;
    }
    public function getGstNumber()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gstnumber = $this->scopeConfig->getValue(self::CONFIG_GST_NUMBER, $storeScope, $this->getStoreid());
        return $gstnumber;
    }
    public function getGstNumberRequiredornot()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gstnumberrequire = $this->scopeConfig->getValue(self::CONFIG_GST_NUMBER_REQUIRED, $storeScope, $this->getStoreid());
        return $gstnumberrequire;
    }
    public function getPanNumber()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gstnumber = $this->scopeConfig->getValue(self::CONFIG_PAN_NUMBER, $storeScope, $this->getStoreid());
        return $gstnumber;
    }
    public function getCinNumber()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gstnumber = $this->scopeConfig->getValue(self::CONFIG_CIN_NUMBER, $storeScope, $this->getStoreid());
        return $gstnumber;
    }
    public function getGstStateConfig()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gststate = $this->scopeConfig->getValue(self::CONFIG_GST_STATE, $storeScope, $this->getStoreid());
        return $gststate;
    }
    public function getGstTaxType()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gststate = $this->scopeConfig->getValue(self::CONFIG_GST_TAXTYPE, $storeScope, $this->getStoreid());

        return $gststate;
    }
    public function getDiscountGstTaxType()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gststate = $this->scopeConfig->getValue(self::CONFIG_DISCOUNT_GST_TAXTYPE, $storeScope, $this->getStoreid());

        return $gststate;
    }
    public function getShippingGstTaxType()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $gststate = $this->scopeConfig->getValue(
            self::CONFIG_GST_SHIPPING_TAXTYPE,
            $storeScope,
            $this->getStoreid()
        );
        return $gststate;
    }

    public function getAuthenticationSignature()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $signature = $this->scopeConfig->getValue(self::CONFIG_GST_SIGNATURE, $storeScope, $this->getStoreid());
        return $signature;
    }
    public function getAuthenticationSignatureText()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $signaturetext = $this->scopeConfig->getValue(self::CONFIG_GST_SIGNATURETEXT, $storeScope, $this->getStoreid());
        return $signaturetext;
    }
    public function getStateCode($address)
    {

        $CustomerRegionId=$address->getRegionId();
        $region =  $this->regiondata->load($CustomerRegionId);
        return $region->getStateCode();
    }
    public function getStateCodeLable($address)
    {

        $CustomerRegionId=$address->getRegionId();
        $region =  $this->regiondata->load($CustomerRegionId);
        return $region->getDefaultName();
    }
    public function getBuyerGst()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        $buyerGst = $this->scopeConfig->getValue(self::CONFIG_GST_BUYERGST, $storeScope, $this->getStoreid());
        return $buyerGst;
    }

    public function getCgstCharge($quote = null)
    {
        $taxPrice=0;
        try {

            if ($this->_checkoutSession->getQuote()->isVirtual()) {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            } else {
                $shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if ($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    } else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if ((!$shippingAddress->getCountryId()) && $quote != null) {
                $shippingAddress = $quote->getShippingAddress();
                $taxPrice= $this->calculateGst($quote->getId());
            } else {
                $taxPrice= $this->calculateGst();
            }
            if ($shippingAddress) {
                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();
                if (!($CountryId=='IN' && $CustomerRegionId==$SystemRegionId)) {
                    $taxPrice=0;
                }
            }
        } catch (\Exception $e) {
        }
        // if ( is_numeric($taxPrice) ) {
        //     return $taxPrice/2;
        // }
        return $taxPrice/2;
        //return $taxPrice/2;
    }
    public function getSgstCharge($quote = null)
    {
        $taxPrice=0;
        try {
            if ($this->_checkoutSession->getQuote()->isVirtual()) {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            } else {
                $shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if ($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    } else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if ((!$shippingAddress->getCountryId()) && $quote != null) {
                $shippingAddress = $quote->getShippingAddress();
                 $taxPrice= $this->calculateGst($quote->getId());
            } else {
                $taxPrice= $this->calculateGst();
            }
            if ($shippingAddress) {
                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();
                if (!($CountryId=='IN' && $CustomerRegionId==$SystemRegionId)) {
                    $taxPrice=0;
                }
            }
        } catch (\Exception $e) {
        }
        return $taxPrice/2;
    }
    public function getIgstCharge($quote = null)
    {
        $taxPrice=0;
        try {
            if ($this->_checkoutSession->getQuote()->isVirtual()) {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            } else {
                $shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if ($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    } else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if ((!$shippingAddress->getCountryId()) && $quote != null) {
                $shippingAddress = $quote->getShippingAddress();
                $taxPrice= $this->calculateGst($quote->getId());
            } else {
                $taxPrice= $this->calculateGst();
            }
            if ($shippingAddress) {
                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();

                if ($CountryId!='IN' || $CustomerRegionId==$SystemRegionId) {
                    $taxPrice=0;
                }
            }
        } catch (\Exception $e) {
        }

        return $taxPrice;
    }
    public function getMaxPercentage()
    {
        $cart = $this->_checkoutSession->getQuote();
        $gstPercent=0;
        foreach ($cart->getAllVisibleItems() as $item) {
            $product=$this->_productloader->create()->load($item->getProductId());
            $gstPercent=$product->getGstSource();
            $gstPercentMinPrice=$product->getGstSourceMinprice();
            $gstPercentAfterMinprice=$product->getGstSourceAfterMinprice();
            if ($gstPercent<=0) {
                $cats = $product->getCategoryIds();
                foreach ($cats as $category_id) {
                    $_cat = $this->_categoryFactory->create()->load($category_id) ;
                    $gstPercent=$_cat->getGstCatSource();
                    $gstPercentMinPrice=$_cat->getGstCatSourceMinprice();
                    $gstPercentAfterMinprice=$_cat->getGstCatSourceAfterMinprice();
                    if ($gstPercent!='') {
                        if ($gstPercentMinPrice > 0) {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                        break;
                    }
                }
            } else {
                if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                    $gstPercent=$gstPercentAfterMinprice;
                }
            }
        }
        return $gstPercent;
    }
    public function calculateGst($quote = null)
    {

        try {
            if (!($this->isModuleEnabled())) {
                return 0;
            }

            $cart = $this->_checkoutSession->getQuote();

            if ($this->_checkoutSession->getQuote()->isVirtual()) {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            } else {
                $shippingAddress =  $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if ($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    } else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if (!$shippingAddress->getCountryId()) {
                $cart = $this->quoteRepository->get($quote);
                $shippingAddress = $cart->getShippingAddress();
            }


            if ($shippingAddress) {

                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();
                if ($CountryId!='IN') {
                    return 0;
                }
                $TotalGstPrice=0;

                if ($this->getGstTaxType()) {
                    $cart->setExclPrice(1);
                } else {

                    $cart->setExclPrice(0);
                }

                if ($this->getShippingGstTaxType()) {
                    $cart->setShipExclPrice(1);
                } else {
                    $cart->setShipExclPrice(0);
                }
                $cart->save();
                foreach ($cart->getAllItems() as $item) {
                    $gstPercent=0;
                    $product=$this->_productloader->create()->load($item->getProductId());
                    if ($product->getTypeId()=="bundle") {
                        if ($product->getPriceType()=='0') {
                            continue;
                        }
                    }
                    
                    $itemPriceAfterDiscount= ($item->getPrice() * $item->getDiscountPercent())/100 ;
                    $prdPrice=$item->getPrice()-$itemPriceAfterDiscount;
                    $gstPercent=$product->getGstSource();
                    $gstPercentMinPrice=$product->getGstSourceMinprice();
                    $gstPercentAfterMinprice=$product->getGstSourceAfterMinprice();
                    if ($gstPercent<=0) {
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = $this->_categoryFactory->create()->load($category_id) ;
                            $gstPercent=$_cat->getGstCatSource();
                            $gstPercentMinPrice=$_cat->getGstCatSourceMinprice();
                            $gstPercentAfterMinprice=$_cat->getGstCatSourceAfterMinprice();
                            if ($gstPercent!='') {
                                if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                                    $gstPercent=$gstPercentAfterMinprice;
                                }
                                break;
                            }
                        }
                    } else {
                        if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    if ($gstPercent<=0) {
                        $gstPercent = $this->getGstTaxperConfig();
                        $gstPercentMinPrice = $this->getGstTaxMinPriceConfig();
                        $gstPercentAfterMinprice = $this->getGstTaxPerMinPriceConfig();
                        if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    $qty          = $item->getQty();
                    $rowTotal     = $item->getRowTotal();
                    $DiscountAmount=$item->getDiscountAmount();
                    
                    if ($gstPercent=='-1') {
                        $gstPercent = 0;
                    }
            

                   
                    if ($this->getGstTaxType()) {
                        $GstPrice= ((($rowTotal)*$gstPercent)/100);
                    } else {
                        $totalPercent = 100 + $gstPercent;
                        $perPrice     = ($rowTotal) / $totalPercent;
                        $GstPrice     = $perPrice * $gstPercent;
                    }

                    $TotalGstPrice+=$GstPrice;
                  
                    if ($CountryId=='IN' && $CustomerRegionId==$SystemRegionId) {
                        $GstPrice=round($GstPrice, 3);
                        $item->setCgstCharge($GstPrice/2);
                        $item->setCgstPercent($gstPercent/2);
                        $item->setSgstCharge($GstPrice/2);
                        $item->setSgstPercent($gstPercent/2);
                    } elseif ($CountryId=='IN' && $CustomerRegionId!=$SystemRegionId) {
                        $item->setIgstCharge($GstPrice);
                        $item->setIgstPercent($gstPercent);
                    }
                    if ($this->getGstTaxType()) {
                        $item->setExclPrice(1);
                    } else {
                        $item->setExclPrice(0);
                    }

                    if ($this->getShippingGstTaxType()) {
                        $cart->setShipExclPrice(1);
                    } else {
                        $cart->setShipExclPrice(0);
                    }
                    $item->save();

                }
                //$this->_checkoutSession->getQuote()->collectTotals()->save();
            }
        } catch (\Exception $e) {
        }
    
         return number_format((float)$TotalGstPrice, 2);
    }
    public function getDiscountGstCharge($quote = null)
    {
            $TotalDiscountGstPrice=0;
        try {
            if (!($this->isModuleEnabled())) {
                return 0;
            }

            $cart = $this->_checkoutSession->getQuote();

            if ($this->_checkoutSession->getQuote()->isVirtual()) {
                $shippingAddress = $this->_checkoutSession->getQuote()->getBillingAddress();
            } else {
                $shippingAddress =  $this->_checkoutSession->getQuote()->getShippingAddress();
                if (!$shippingAddress->getCountryId()) {
                    $cart = $this->backendQuoteSession->getQuote();
                    if ($cart->isVirtual()) {
                        $shippingAddress = $cart->getBillingAddress();
                    } else {
                        $shippingAddress = $cart->getShippingAddress();
                    }
                }
            }
            if (!$shippingAddress->getCountryId()) {
                $cart = $this->quoteRepository->get($quote);
                $shippingAddress = $cart->getShippingAddress();
            }
            
            if ($shippingAddress) {
                $CountryId=$shippingAddress->getCountryId();
                $CustomerRegionId=$shippingAddress->getRegionId();
                $SystemRegionId=$this->getGstStateConfig();
                if ($CountryId!='IN') {
                    return 0;
                }
               
                $cart->save();
                foreach ($cart->getAllItems() as $item) {
                    $gstPercent=0;
                    $product=$this->_productloader->create()->load($item->getProductId());
                    if ($product->getTypeId()=="bundle") {
                        if ($product->getPriceType()=='0') {
                            continue;
                        }
                    }
                    
                    $itemPriceAfterDiscount= ($item->getPrice() * $item->getDiscountPercent())/100 ;
                    $prdPrice=$item->getPrice()-$itemPriceAfterDiscount;
                    $gstPercent=$product->getGstSource();
                    $gstPercentMinPrice=$product->getGstSourceMinprice();
                    $gstPercentAfterMinprice=$product->getGstSourceAfterMinprice();
                    if ($gstPercent<=0) {
                        $cats = $product->getCategoryIds();
                        foreach ($cats as $category_id) {
                            $_cat = $this->_categoryFactory->create()->load($category_id) ;
                            $gstPercent=$_cat->getGstCatSource();
                            $gstPercentMinPrice=$_cat->getGstCatSourceMinprice();
                            $gstPercentAfterMinprice=$_cat->getGstCatSourceAfterMinprice();
                            if ($gstPercent!='') {
                                if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                                    $gstPercent=$gstPercentAfterMinprice;
                                }
                                break;
                            }
                        }
                    } else {
                        if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    if ($gstPercent<=0) {
                        $gstPercent = $this->getGstTaxperConfig();
                        $gstPercentMinPrice = $this->getGstTaxMinPriceConfig();
                        $gstPercentAfterMinprice = $this->getGstTaxPerMinPriceConfig();
                        if ($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice) {
                            $gstPercent=$gstPercentAfterMinprice;
                        }
                    }
                    $qty          = $item->getQty();
                    $rowTotal     = $item->getDiscountAmount();
                    
                    if ($gstPercent=='-1') {
                        $gstPercent = 0;
                    }
                    $GstPrice = 0 ;
                    if (!$this->getDiscountGstTaxType()) {
                        $totalPercent = 100 + $gstPercent;
                        $perPrice     = ($rowTotal) / $totalPercent;
                        $GstPrice     = $perPrice * $gstPercent;
                    }
                
                    $TotalDiscountGstPrice+=$GstPrice;
                    $item->setDiscountAmount($rowTotal-$GstPrice);
                    $item->setBaseDiscountAmount($rowTotal-$GstPrice);
                }
               //$this->_checkoutSession->getQuote()->collectTotals()->save();
            }
        } catch (\Exception $e) {
        }
        return number_format((float)$TotalDiscountGstPrice, 2);
    }
    public function getShippingCgstCharge($quote = null)
    {
        try {
            $address =  $this->_checkoutSession->getQuote()->getShippingAddress();
            $quote =  $this->_checkoutSession->getQuote();
            if (!$address->getCountryId()) {
                $address = $this->backendQuoteSession->getQuote()->getShippingAddress();
                $quote =  $this->backendQuoteSession->getQuote();
            }

            if ($address) {
                $countryId = $address->getCountryId();
                $customerRegionId = $address->getRegionId();
                $systemRegionId = $this->getGstStateConfig();
                $maxGstPercent = $gstPercent = 0;
                foreach ($quote->getAllItems() as $item) {

                    if ($countryId == 'IN' && $customerRegionId==$systemRegionId) {
                        $gstPercent = $item->getCgstPercent();

                    }
                    if ($gstPercent > $maxGstPercent) {
                        $maxGstPercent = $gstPercent;
                    }
                }
                if ($this->getShippingGstTaxType()) {
                    $shippingGst = $address->getShippingAmount() * ($maxGstPercent/100);

                } else {
                    $shippingGstTotal = 100 + $maxGstPercent;
                    $shippingGstPeracent = $address->getShippingAmount() / $shippingGstTotal;
                    $shippingGst = $shippingGstPeracent * $maxGstPercent;
                }
            }

        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
        return $shippingGst;
    }

    public function getShippingSgstCharge($quote = null)
    {
        try {
            $address =  $this->_checkoutSession->getQuote()->getShippingAddress();
            $quote =  $this->_checkoutSession->getQuote();
            if (!$address->getCountryId()) {
                $address = $this->backendQuoteSession->getQuote()->getShippingAddress();
                $quote =  $this->backendQuoteSession->getQuote();
            }
            if ($address) {
                $countryId = $address->getCountryId();
                $customerRegionId = $address->getRegionId();
                $systemRegionId = $this->getGstStateConfig();
                $maxGstPercent = $gstPercent = 0;
                foreach ($quote->getAllItems() as $item) {

                    if ($countryId == 'IN' && $customerRegionId==$systemRegionId) {
                        $gstPercent = $item->getCgstPercent();

                    }
                    if ($gstPercent > $maxGstPercent) {
                        $maxGstPercent = $gstPercent;
                    }
                }
                if ($this->getShippingGstTaxType()) {
                    $shippingGst = $address->getShippingAmount() * ($maxGstPercent/100);

                } else {
                    $shippingGstTotal = 100 + $maxGstPercent;
                    $shippingGstPeracent = $address->getShippingAmount() / $shippingGstTotal;
                    $shippingGst = $shippingGstPeracent * $maxGstPercent;
                }
            }

        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
        return $shippingGst;
    }

    public function getShippingIgstCharge($quote = null)
    {
        try {
            $address =  $this->_checkoutSession->getQuote()->getShippingAddress();
            $quote =  $this->_checkoutSession->getQuote();
            if (!$address->getCountryId()) {
                $address = $this->backendQuoteSession->getQuote()->getShippingAddress();
                $quote =  $this->backendQuoteSession->getQuote();
            }
            if ($address) {
                $countryId = $address->getCountryId();

                $customerRegionId = $address->getRegionId();
                $systemRegionId = $this->getGstStateConfig();
                
                $maxGstPercent = $gstPercent = 0;
                foreach ($quote->getAllItems() as $item) {

                    if ($countryId == 'IN' && $customerRegionId!=$systemRegionId) {
                        $gstPercent = $item->getIgstPercent();
                    }
                    if ($gstPercent > $maxGstPercent) {
                        $maxGstPercent = $gstPercent;
                    }
                }

                if ($this->getShippingGstTaxType()):
                    $shippingGst = $address->getShippingAmount() * ($maxGstPercent/100);

                else:
                    $shippingGstTotal = 100 + $maxGstPercent;
                    $shippingGstPeracent = $address->getShippingAmount() / $shippingGstTotal;
                    $shippingGst = $shippingGstPeracent * $maxGstPercent;
                endif;
            }

        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }

        return $shippingGst;
    }
    public function getProductgstdata($productid)
    {
        $product=$this->_productloader->create()->load($productid);
        if ($product->getTypeId()=="bundle") {
            if ($product->getPriceType()=='0') {
                return false;
            }
                return true;
        }
    }
    public function getProductData($productid)
    {
        $product=$this->productdatabyrepository->getById($productid);
        return $product;
    }
    public function getCheckoutSession()
    {
        
        return $this->_checkoutSession;
    }
}
