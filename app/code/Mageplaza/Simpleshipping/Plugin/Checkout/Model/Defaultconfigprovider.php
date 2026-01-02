<?php

namespace Mageplaza\Simpleshipping\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\Address\RateRequest;

class Defaultconfigprovider

{

/**

 * @var CheckoutSession

 */
    protected $cart;
protected $checkoutSession;
    protected $request;

    /**
     * @var Cart
     */

/**

 * Constructor

 *

 * @param CheckoutSession $checkoutSession

 */

public function __construct(
        Cart $cart,
        RateRequest $request,
CheckoutSession $checkoutSession

) {

$this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->request = $request; 
}




public function afterGetConfig(

\Magento\Checkout\Model\DefaultConfigProvider $subject,

array $result

) {


     
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/plugins_method.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
     
     
        $items = $result['totalsData']['items'];
        
        $quote = $this->cart->getQuote();
        $list_items = $quote->getAllItems();

        $i = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode();

        foreach ($list_items as $item) {

            $productId = $item->getProductId();
            $product = $item->getProduct();
            $getQunty = $item->getQty();
            $product->load($productId);
            $strest = 0;
            if (!$item->getParentItemId()) {
                if ($item->getProductType() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $productId = $item->getProductId();
                    $children = $item->getChildren();
                    foreach ($children as $childItem) {
                        $childProductId = $childItem->getProductId();
                        $parentProductId = $item->getProduct()->getId();
                        $childProduct = $childItem->getProduct();
                        $product->load($childProductId);
                        $shippingChargesFeatureEnabled  = $product->getData('shipping_charges_feature_enabl');
                        $WeightPrice = $product->getData('weight_price');
                        if ($shippingChargesFeatureEnabled == 1 && ($WeightPrice != null && $WeightPrice != "" && $currencyCode == "INR")) {
                            $strest = $WeightPrice * $getQunty;
                        } elseif ($shippingChargesFeatureEnabled== 1) {
                            $strest = " To Be Quoted";
                            
                        } else {
                            $strest = "";
                        }
                        $logger->info("getProductType-strest:" . $strest);
                        $logger->info("getProductType-childProductId:" . $childProductId);
                        $logger->info("getProductType-shippingChargesFeatureEnabled:" . $shippingChargesFeatureEnabled);
                    }

                    // $logger->info('product Name: ' . $item->getParentItemId());

                } elseif ($item->getParentItemId()) {
                    // Simple product child case
                    $parentItem = $item->getParentItem();
                    $productId = $parentItem->getProductId();
                  
                    $childProductId = $productId;
                    $product->load($childProductId);
                    $shippingChargesFeatureEnabled  = $product->getData('shipping_charges_feature_enabl');
                    $WeightPrice = $product->getData('weight_price');
                    if ($shippingChargesFeatureEnabled == 1
                        && ($WeightPrice != null && $WeightPrice != "" && $currencyCode == "INR")
                    ) {
                        $strest = $WeightPrice * $getQunty;
                    } elseif ($shippingChargesFeatureEnabled == 1) {
                        $strest = " To Be Quoted";
                    } else {
                        $strest = "";
                    }
                    $logger->info("getParentItemId-strest:" . $strest);
                    $logger->info("getParentItemId-childProductId:" . $childProductId);
                } else {
                    // Normal product case (not configurable or child)
                    $parentProductId = $productId;
                    $childProductId = $productId;
                    $product->load($childProductId);
                    $shippingChargesFeatureEnabled  = $product->getData('shipping_charges_feature_enabl');
                    $WeightPrice = $product->getData('weight_price');
                    if ($shippingChargesFeatureEnabled == 1 && ($WeightPrice != null && $WeightPrice != "" && $currencyCode == "INR")) {
                        $strest = $WeightPrice * $getQunty;
                    } elseif ($shippingChargesFeatureEnabled == 1) {
                        $strest = " To Be Quoted";
                    } else {
                        $strest = "";
                    }
                    $logger->info("strest:" . $strest);
                    $logger->info("childProductId:" . $childProductId);
                }
               
              

               // $items[$i]['customshippingcharge'] = $strest;
                $result['quoteItemData'][$i]['customshippingcharge'] = $strest;


                $i++;
            }
        }



// foreach ($items as $index => $item) {

// $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);

// $productId = $quoteItem->getProductId();


// $weightPrice = "009";
           
//             $logger->info('weightPrice: ' . $quoteItem->getProductId()."-".$weightPrice."-". $quoteItem->getProduct()->getWeightPrice());
// //$result['quoteItemData'][$index]['customshippingcharge'] = $quoteItem->getProductId()."-". $weightPrice;

// }

return $result;

}

}