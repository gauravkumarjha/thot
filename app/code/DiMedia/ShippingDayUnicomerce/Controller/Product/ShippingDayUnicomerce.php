<?php

namespace DiMedia\ShippingDayUnicomerce\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Swatches\Helper\Data as SwatchHelper;
use DiMedia\ShippingDayUnicomerce\Helper\Data as ShippingHelper;

class ShippingDayUnicomerce extends Action
{
    protected $productFactory;
    protected $resultJsonFactory;
    protected $ConfigurableProductType;
    protected $productRepository;
    protected $SwatchHelper;
    protected $shippingHelper;

    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        ConfigurableProductType $ConfigurableProductType,
        ProductRepositoryInterface $ProductRepositoryInterface,
        SwatchHelper $SwatchHelper,
        JsonFactory $resultJsonFactory,
        ShippingHelper $shippingHelper
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->ConfigurableProductType = $ConfigurableProductType;
        $this->SwatchHelper = $SwatchHelper;
        $this->productRepository = $ProductRepositoryInterface;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->shippingHelper = $shippingHelper;
    }

    public function execute()
    {
       
        $result = $this->resultJsonFactory->create();

         $productId = $this->getRequest()->getParam('product_id');
         $swatchProductId = $this->getRequest()->getParam('swatch_product_id');
        $qty = $this->getRequest()->getParam('qty');
        $product = $this->productFactory->create()->load($productId);
        if($swatchProductId != "" ) {
            $productId =  $this->getchildProductid($productId, $product, $swatchProductId);
            $product = $this->productFactory->create()->load($productId);

        }
        if (!$productId) {
            return $result->setData(['success' => false, 'message' => __('Invalid Product IDs')]);
        }

        try {
            // Load the product by ID
          
            
             $inventory_1 = $product->getData('inventory_1');
            $inventory_2 = $product->getData('inventory_2'); 
            $inventory_3 = $product->getData('inventory_3');


            $requestedQty = $qty;
            $timeline = "";
            if ($inventory_1 != "0" && !empty($inventory_1) && $inventory_1 >= $requestedQty) {
                 $requestedQty = $inventory_1 - $requestedQty;
                 $timeline = $product->getData('timeline_1');
                 $requestedQty = 0;
            } elseif ($requestedQty != 0 && $inventory_1 < $requestedQty && $inventory_1 > 0) {
                $requestedQty -= $inventory_1;
                $timeline = $product->getData('timeline_1');
            }
            

            if ($requestedQty != 0 && $inventory_2 != "0" && !empty($inventory_2) && $inventory_2 >= $requestedQty
            ) {
                $timeline = $product->getData('timeline_2');
                $requestedQty = $inventory_2 - $requestedQty;
                $requestedQty = 0;
            } elseif ($requestedQty != 0 && $inventory_2 < $requestedQty && $inventory_2 > 0) {
                $requestedQty -= $inventory_2;
                $timeline = $product->getData('timeline_2');
              
            }

            if ($inventory_3 != "0" && $requestedQty != 0 && !empty($inventory_3) && $inventory_3 >= $requestedQty
            ) {
                $timeline = $product->getData('timeline_3');
                $requestedQty = $inventory_3 - $requestedQty;
                $requestedQty = 0;
            } elseif ( $requestedQty != 0 &&  $inventory_3 < $requestedQty && $inventory_3 > 0) {
                $timeline = $product->getData('timeline_3');
               
            }
           
            // if($inventory_1 != 0 && $inventory_1 != null && $inventory_1 != "") {
            //     $timeline = $product->getData('timeline_1');
            // } else if($inventory_2 != 0 && $inventory_2 != null && $inventory_2 != "") {
            //     $timeline = $product->getData('timeline_2');
            // } else if($inventory_3 != 0 && $inventory_3 != null && $inventory_3 != "") {
            //     $timeline = $product->getData('timeline_3');
            // }
            // Get some attribute data (example: name and price)
            if($timeline != "") {
               // $days = $this->shippingHelper->getShippingDays();
                $timeline = 2+$timeline ;
                $timeline = "Your shipment will be Dispatch in " . $timeline . " days.";
            }
            $attributeData = [
                'shipping_message' => $timeline
            ];

            return $result->setData(['success' => true, "product_id"=> $productId, 'attribute_data' => $attributeData]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    function getchildProductid($configurableProductId, $product,$optionid) {
        $configurableProduct = $product;
        $childProductIds = $this->ConfigurableProductType->getChildrenIds($configurableProductId);
        $productidsdi = [];
        foreach ($childProductIds[0] as $childId) {
            $childProduct = $this->productRepository->getById($childId);

            // Get the configurable attributes
            $configurableAttributes = $this->ConfigurableProductType->getConfigurableAttributesAsArray($configurableProduct);

            foreach ($configurableAttributes as $attribute) {
                $attributeCode = $attribute['attribute_code'];
                $attributeId = $attribute['attribute_id'];
                $optionId = $childProduct->getData($attributeCode);

                // Get swatch information for the option
                $swatchData = $this->SwatchHelper->getSwatchesByOptionsId([$optionId]);

                if (!empty($swatchData)) {
                    foreach ($swatchData as $swatch) {
                        $productidsdi[$optionId] = $childId;
                        //echo "Swatch ID: " . $swatch['value'] . " <br>for option ID: " . $optionId . PHP_EOL . "<br>";
                    }
                }
            }
        }
        $getproductid =  $productidsdi[$optionid];
        return $getproductid;
    }

}
