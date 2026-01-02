<?php
    namespace Dimedia\Utility\Observer;

    use Magento\Framework\Event\Observer;
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Checkout\Model\Session as CheckoutSession;
    use Magento\Quote\Api\CartRepositoryInterface;

use Magento\Catalog\Api\ProductRepositoryInterface;
    use Psr\Log\LoggerInterface;

    class RestrictCod implements ObserverInterface
    {
        protected $logger;
        protected $checkoutSession;
        protected $cartRepository;
        protected $ResourceConnection;
    protected $productRepository;

        
        public function __construct(
        LoggerInterface $logger,
            CheckoutSession $checkoutSession,
        ProductRepositoryInterface $productRepository,
            \Magento\Framework\App\ResourceConnection $ResourceConnection,
            CartRepositoryInterface $cartRepository
        ) {
            $this->logger = $logger;
            $this->checkoutSession = $checkoutSession;
            $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
            $this->resourceConnection    = $ResourceConnection;
        }
 
        public function execute(Observer $observer)
        {
          
            $method = $observer->getEvent()->getMethodInstance();
        //     $result = $observer->getEvent()->getResult();

        // $payemntexplode = [];
        // $payemntexplode2 = [];
                
        //         $quote = $this->checkoutSession->getQuote();
        //         $items = $quote->getAllItems();
        //         foreach ($items as $item) {
        //             $productId =  $item->getProductId();
        //              $product = $this->productRepository->getById($productId);
        //              $attributeValue = $product->getData("cod_enable");
        //     $productType = $product->getTypeId(); // Gets the product type
        //    if ($productType == "configurable") continue;
        //   //  $this->logger->info("prodcut name start-$productId-" . $attributeValue);
        //     if ($attributeValue != "") {
        //         $payemntexplode[]=  $productId."-".$attributeValue;
        //         $payemntexplode2[] =  $attributeValue;
        //     } else {
        //         $payemntexplode[] =  $productId . "-" . 0;
        //         $payemntexplode2[] =  0; 
        //     }
        //             // $this->logger->info('Product Name: ' . $item->getName());
        //             // $this->logger->info('Quantity: ' . $item->getQty());
        //             // $this->logger->info('Price: ' . $item->getPrice());
        //         }
        
        // // $this->logger->info("paymentmethod_cod_custom_1 start-" . print_r($payemntexplode,true));
        // // //$method = $objectManager->create('Magento\OfflinePayments\Model\Cashondelivery');
        // // $this->logger->info("gk 14-01-2025 method-". $method->getCode());
        // if ( $method->getCode() === 'msp_cashondelivery') {
        //     $quote = $this->checkoutSession->getQuote();
        // //    $attributeValue = $quote->getShippingAddress()->getData('your_attribute_code');

        //     // Disable COD if the attribute value is 1
        //     if (in_array("0", $payemntexplode2)) {
        //         $result->setData('is_available', false);
        //     } else {
        //         $result->setData('is_available', true);
        //     }
        // }

      
            
        }
    }
