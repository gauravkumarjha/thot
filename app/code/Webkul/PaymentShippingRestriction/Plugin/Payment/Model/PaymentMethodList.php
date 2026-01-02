<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PaymentShippingRestriction\Plugin\Payment\Model;

use Magento\Payment\Api\Data\PaymentMethodInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Cart;

class PaymentMethodList
{
    /**
     * @var \Magento\Payment\Api\Data\PaymentMethodInterfaceFactory
     */
    private $methodFactory;

    /**
     * @var\Magento\Quote\Api\Data\ShippingMethodInterface $shippingMethodInterface
     */
    private $shippingMethodInterface;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\ActionFactory
     */
    private $productAction;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     */
    private $productCollection;

    /**
     * @param \Webkul\PaymentShippingRestriction\Helper\Data $shippingHelper
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Payment\Api\Data\PaymentMethodInterfaceFactory $methodFactory
     * @param \Magento\Quote\Api\Data\ShippingMethodInterface $shippingMethodInterface
     * @param \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productAction
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     */
    public function __construct(
        \Webkul\PaymentShippingRestriction\Helper\Data $shippingHelper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Payment\Api\Data\PaymentMethodInterfaceFactory $methodFactory,
        \Magento\Quote\Api\Data\ShippingMethodInterface $shippingMethodInterface,
        \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productAction,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    ) {
        $this->shippingHelper=$shippingHelper;
        $this->cart=$cart;
        $this->methodFactory = $methodFactory;
        $this->shippingMethodInterface = $shippingMethodInterface;
        $this->_productAction = $productAction;
        $this->_productCollection=$productCollection;
    }

    /**
     * around get Active list
     *
     * @param \Magento\Payment\Model\PaymentMethodList $subject
     * @param callable $proceed
     * @param int $storeId
     * @return void
     */
    public function aroundGetActiveList(
        \Magento\Payment\Model\PaymentMethodList $subject,
        callable $proceed,
        $storeId
    ) {
        $result = [];
        $paymentArray=[];
        $paymentMethodArray = [];
        $methodList = $proceed($storeId);
        if (!$this->shippingHelper->getModuleEnabled()) {
            $paymentArray =  $this->defaultMethodList($methodList);
            return $paymentArray;
        }
        $quoteItems = $this->shippingHelper->getCurrentQuoteInfo();
        $productIds = $this->getCartProductIds($quoteItems);
        $collection =  $this->_productCollection->create()
        ->addFieldToFilter('entity_id', ['in' =>$productIds]);
        $attributeCode = "product_payment_attribute";
        foreach ($collection as $product) {
            $paymentAttributeValue = $this->_productAction->create()
            ->getAttributeRawValue(
                $product->getEntityId(),
                $attributeCode,
                $storeId
            );
            if (empty($paymentAttributeValue)) {
                $categoryIds=$product->getCategoryIds();
                $categoryAttributeData = $this->shippingHelper->getCategoryPaymentAttribute($categoryIds);
                if (!empty($categoryAttributeData)) {
                    $paymentMethodArray[$product->getEntityId()]= $categoryAttributeData;
                }
            } else {
                $allowedPaymentArr=explode(',', $paymentAttributeValue);
                $paymentMethodArray[$product->getEntityId()] = $allowedPaymentArr;
            }
        }
        $totalCount=count($paymentMethodArray);
        if (empty($totalCount)) {
            foreach ($methodList as $paymentCode) {
                $paymentArray[$paymentCode->getCode()]=$paymentCode;
            }
            return array_values($paymentArray);
        } elseif ($totalCount==1) {
            $commonPayment = $this->getPaymentOnSingleProduct($paymentMethodArray);
        } else {
            $commonPayment =  $this->shippingHelper->getCommonOptionsFromArray($paymentMethodArray);
        }
        $mappedArray = $this->shippingHelper->getMappedPaymentMethodsFromShipping();
        $result = $commonPayment;
        if (!empty($mappedArray)) {
            $result = array_intersect($mappedArray, $commonPayment);
        }
        foreach ($methodList as $paymentCode) {
            if (in_array($paymentCode->getCode(), $result)) {
                $paymentArray[$paymentCode->getCode()]=$paymentCode;
            }
        }
        return array_values($paymentArray);
    }

     /**
      * return Product id
      *
      * @param array $quoteItems
      * @return array
      */
    public function getCartProductIds($quoteItems)
    {
        $allowedPaymentProductType=['simple','downloadable','virtual'];
        $productIds=[];
        if (!empty($quoteItems)) {
            foreach ($quoteItems as $item) {
                if (in_array($item->getProductType(), $allowedPaymentProductType)) {
                    array_push($productIds, $item->getProductId());
                }
            }
        }
        return $productIds;
    }

    /**
     * return default array
     *
     * @param array $methodList
     * @return void
     */
    private function defaultMethodList($methodList)
    {
        if (!empty($methodList)) {
            foreach ($methodList as $paymentCode) {
                $paymentArray[$paymentCode->getCode()]=$paymentCode;
            }
            return array_values($paymentArray);
        }
    }
    /**
     * get allowed payment on single product
     *
     * @param array $allowedPayment
     * @return array
     */
    public function getPaymentOnSingleProduct($allowedPayment)
    {
        $commonPayment = [];
        if (!empty($allowedPayment)) {
            foreach ($allowedPayment as $paymentMethod) {
                $commonPayment = $paymentMethod;
            }
        }
        return $commonPayment;
    }
}
