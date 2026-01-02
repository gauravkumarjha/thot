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

/**
 *  plugin validate credits to assign each customer group just once
 */
namespace Webkul\PaymentShippingRestriction\Plugin;

use Webkul\PaymentShippingRestriction\Helper\Data;

class ShippingPlugin
{
   /**
    * @var \Webkul\PaymentShippingRestriction\Helper\Data $helper
    */
    protected $_helper;

   /**
    * @var \Magento\Framework\Json\Helper\Data $jsonHelper
    */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\ActionFactory
     */
    protected $productAction;

    /**
     * @param \Webkul\PaymentShippingRestriction\Helper\Data $helper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productAction
     */
    public function __construct(
        \Webkul\PaymentShippingRestriction\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\ActionFactory $productAction
    ) {
    
        $this->jsonHelper = $jsonHelper;
        $this->_helper = $helper;
        $this->_productCollection=$productCollection;
        $this->_scopeConfig = $scopeConfig;
        $this->_productAction = $productAction;
    }

    public function aroundCollectRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ) {
        $productIds=[];
        $commonShipping=[];
        $allowedShipping=[];
        $storeId = $request->getStoreId();
        // customization chetaru start
        // if ($this->_helper->getModuleEnabled()) {
        //     $quoteItems = $this->_helper->getCurrentQuoteInfo();
        //     $productIds = $this->getCartProductIds($quoteItems);
        //     $collection =  $this->_productCollection->create()
        //     ->addFieldToFilter('entity_id', ['in' =>$productIds]);
        //     $allowedShipping = $this->getAllowedShippingOnProduct($collection, $storeId);
        //      $totalCount = count($allowedShipping);
        //     if (empty($totalCount)) {
        //         $result = $proceed($request);
        //         return $result;
        //     } elseif ($totalCount==1) {
        //         $commonShipping = $this->getShippingOnSingleProduct($allowedShipping);
        //     } else {
        //         $commonShipping = $this->_helper->getCommonOptionsFromArray($allowedShipping);
        //     }
        //     $limitCarrier = $request->getLimitCarrier();
        //     if (!$limitCarrier) {
        //         $carriers = $this->_scopeConfig->getValue(
        //             'carriers',
        //             \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        //             $storeId
        //         );
        //         foreach ($carriers as $carrierCode => $carrierConfig) {
        //             if (in_array($carrierCode, $commonShipping)) {
        //                 $subject->collectCarrierRates($carrierCode, $request);
        //             }
        //         }
        //     } else {
        //         if (!is_array($limitCarrier)) {
        //             $limitCarrier = [$limitCarrier];
        //         }
                
        //         foreach ($limitCarrier as $carrierCode) {
        //             $carrierConfig = $this->_scopeConfig->getValue(
        //                 'carriers/' . $carrierCode,
        //                 \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        //                 $storeId
        //             );
        //             if (!$carrierConfig) {
        //                 continue;
        //             }
        //             $subject->collectCarrierRates($carrierCode, $request);
        //         }
        //     }
        //     return $subject;
        // }
        // customization chetaru end
        $result = $proceed($request);
        return $result;
    }

    /**
     * allowed shipping on product
     *
     * @param collection $productCollection
     * @param int $storeId
     * @return array
     */
    public function getAllowedShippingOnProduct($productCollection, $storeId)
    {
        $shippingArray = [];
        $categoryIds = [];
        $shippingAttributeValue = "";
        if (!empty($productCollection)) {
            $attributeCode = "product_shipping_attribute";
            foreach ($productCollection as $product) {
                $shippingAttributeValue = $this->_productAction->create()
                ->getAttributeRawValue(
                    $product->getEntityId(),
                    $attributeCode,
                    $storeId
                );
                if (empty($shippingAttributeValue)) {
                    $categoryIds = $product->getCategoryIds();
                    $shippingArray[$product->getEntityId()]= $this->_helper->getCategoryShippingAttribute(
                        $categoryIds,
                        $storeId
                    );
                } else {
                    $allowedShippingArray = explode(',', $shippingAttributeValue);
                    $shippingArray[$product->getEntityId()] = $allowedShippingArray;
                }
            }
        }
        return $shippingArray;
    }

    /**
     * get allowed shipping on single product
     *
     * @param array $allowedShipping
     * @return array
     */
    public function getShippingOnSingleProduct($allowedShipping)
    {
        $commonShipping = [];
        if (!empty($allowedShipping)) {
            foreach ($allowedShipping as $shipping) {
                $commonShipping = $shipping;
            }
        }
        return $commonShipping;
    }

    /**
     * return Product id
     *
     * @param array $quoteItems
     * @return array
     */
    public function getCartProductIds($quoteItems)
    {
        $allowedShippingProductType=['simple'];
        $productIds=[];
        if (!empty($quoteItems)) {
            foreach ($quoteItems as $item) {
                if (in_array($item->getProductType(), $allowedShippingProductType)) {
                    array_push($productIds, $item->getProductId());
                }
            }
        }
        return $productIds;
    }
}
