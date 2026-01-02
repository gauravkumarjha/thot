<?php
/*
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright(c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PaymentShippingRestriction\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Helper Data
 */
class Data extends AbstractHelper
{

    /**
     * @var  \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $category;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Payment\Model\Config $paymentConfig
     */
    protected $paymentConfig;

    /**
     * @var \Magento\Eav\Model\Config $eavConfig,
     */
    protected $eavConfig;

    /**
     * @var \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory
     */
    protected $shippingMappingFactory;

   /**
    * @param Context $context
    * @param \Magento\Catalog\Model\ProductFactory $productFactory
    * @param \Magento\Checkout\Model\Cart $cart
    * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param \Magento\Payment\Model\Config $paymentConfig
    * @param \Magento\Eav\Model\Config $eavConfig
    * @param \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory $shippingMappingFactory
    */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Eav\Model\Config $eavConfig,
        \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory $shippingMappingFactory
    ) {
        $this->_productFactory      =   $productFactory;
        $this->cart=$cart;
        $this->category=$categoryCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_paymentConfig=$paymentConfig;
        $this->eavConfig = $eavConfig;
        $this->shippingMappingFactory = $shippingMappingFactory;
        parent::__construct($context);
    }

    /**
     * configuration values
     *
     * @param string $field
     * @param int $storeId
     * @return void
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * return module enabled or not
     *
     * @return boolean
     */
    public function getModuleEnabled()
    {
        return  $this->scopeConfig->getValue(
            'shippingrestriction/enable/status',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return current quote info
     *
     * @return array
     */
    public function getCurrentQuoteInfo()
    {
        $cartItems=$this->cart->getQuote()->getItemsCollection();
        return $cartItems;
    }

    /**
     * shipping on category
     *
     * @param array $categoryArray
     * @param int $storeId
     * @return array
     */
    public function getCategoryShippingAttribute($categoryArray, $storeId)
    {
        $shippingString="";
        $categoryShippingArray=[];
        $categoryCollection= $this->category->create()->addAttributeToSelect('category_shipping_attribute')
        ->addFieldToFilter('entity_id', ['in' =>$categoryArray]);
        if ($categoryCollection->getSize()>0) {
            foreach ($categoryCollection as $category) {
                if (!empty($category->getCategoryShippingAttribute())) {
                    $shippingString.=$category->getCategoryShippingAttribute().',';
                    $categoryShippingArray=array_unique(explode(',', trim($shippingString, ',')));
                }
            }
            if (empty($categoryShippingArray)) {
                $categoryShippingArray = $this->getDefaultShippingMethodCodes($storeId);
            }
        }
        return $categoryShippingArray;
    }

    /**
     * return allowed payment on categories
     *
     * @param array $categoryArr
     * @return array
     */
    public function getCategoryPaymentAttribute($categoryArray)
    {
        $paymentString="";
        $categoryPaymentArray=[];
        $categoryCollection= $this->category->create()->addAttributeToSelect('category_payment_attribute')
        ->addFieldToFilter('entity_id', ['in' =>$categoryArray]);
        if ($categoryCollection->getSize()>0) {
            foreach ($categoryCollection as $category) {
                if (!empty($category->getCategoryPaymentAttribute())) {
                    $paymentString.=$category->getCategoryPaymentAttribute().',';
                    $categoryPaymentArray=array_unique(explode(',', trim($paymentString, ',')));
                }
            }
            if (empty($categoryPaymentArray)) {
                $categoryPaymentArray = $this->getDefaultPaymentMethodCodes();
            }
        }
        return $categoryPaymentArray;
    }

   /**
    * get default shipping method codes
    *
    * @param int $storeId
    * @return array
    */
    public function getDefaultShippingMethodCodes($storeId)
    {
        $defaultActiveCarriers=[];
        $carriers = $this->_scopeConfig->getValue(
            'carriers',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        // customization chetaru start
        // foreach ($carriers as $carrierCode => $carrierArray) {
        //     if ($carrierArray['active']==1) {
        //         array_push($defaultActiveCarriers, $carrierCode);
        //     }
        // }
        // customization chetaru end
        return $defaultActiveCarriers;
    }

   /**
    * return payment method code
    *
    * @return array
    */
    public function getDefaultPaymentMethodCodes()
    {
        $defaultPaymentMethodArray=[];
        $payments = $this->_paymentConfig->getActiveMethods();
        foreach ($payments as $paymentCode => $paymentArray) {
            array_push($defaultPaymentMethodArray, $paymentCode);
        }
        return $defaultPaymentMethodArray;
    }

    /**
     * common payment/shipping options
     *
     * @param array $inputArray
     * @return array
     */
    public function getCommonOptionsFromArray($inputArray)
    {
        $result = [];
        if (!empty($inputArray)) {
            $flag = false;
            foreach ($inputArray as $item) {
                if ($flag) {
                    $result = array_intersect($result, $item);
                } else {
                    $result = $item;
                }
                $flag = true;
            }
        }
        return $result;
    }

    /**
     * get all shipping options
     *
     * @return void
     */
    public function getAllShippingOptions()
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'product_shipping_attribute');
        $options = $attribute->getSource()->getAllOptions();
        return $options;
    }

    /**
     * get all shipping options
     *
     * @return array
     */
    public function getAllPaymentOptions()
    {
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'product_payment_attribute');
        $options = $attribute->getSource()->getAllOptions();
        return $options;
    }

    /**
     * get Mapped Payment Methoods From shipping code
     *
     * @return array
     */
    public function getMappedPaymentMethodsFromShipping()
    {
        try {
            $mappedPaymentCodes = [];
             $shippingCode = $this->getCurrentShippingMethodFromCheckout();
            if (!empty($shippingCode)) {
                $mappingCollection = $this->shippingMappingFactory->create()->getCollection()
                ->addFieldToFilter(
                    'shipping_code',
                    ['eq' => $shippingCode]
                );
                if (!empty($mappingCollection)) {
                    foreach ($mappingCollection as $payment) {
                        array_push($mappedPaymentCodes, $payment->getPaymentCode());
                    }
                }
            }
            return $mappedPaymentCodes;
        } catch (\Exception $e) {
            return $mappedPaymentCodes;
        }
    }

    /**
     * get currently selected shipping code from checkout
     *
     * @return string
     */
    public function getCurrentShippingMethodFromCheckout()
    {
        $shippingCode = '';
        $quote = $this->cart->getQuote();
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        $searchString = '_';
        if (strpos($shippingMethod, $searchString) !== false) {
            $shippingCodes =  explode('_', $shippingMethod);
            $shippingCode = $shippingCodes[0];
        } else {
            $shippingCode = $shippingMethod;
        }
        return $shippingCode;
    }
}
