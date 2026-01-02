<?php

namespace DiMedia\SendProductToCrm\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\CatalogInventory\Api\StockRegistryInterface;



class SendProductToCrm implements ObserverInterface
{
    protected $curl;
    protected $logger;
    protected $categoryRepository;
    protected $resource;
    protected $storeManager;
    protected $_productFactory;
    protected $productRepository;
    protected $configurableProductType;
    protected $stockRegistry;
    public function __construct(
        Curl $curl,
        LoggerInterface $logger,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
         ProductRepositoryInterface $productRepository,
        Configurable $configurableProductType,
        ProductFactory $productFactory,
         StockRegistryInterface $stockRegistry,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->curl = $curl;
        $this->logger = $logger;
        $this->categoryRepository = $categoryRepository;
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->configurableProductType = $configurableProductType;
        $this->_productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
    }

    public function execute(Observer $observer)
    {
        try {
            /** @var ProductInterface $product */
            $product = $observer->getEvent()->getProduct();

            // Get category names as a comma-separated string
            $categoriesString = $this->getCategoryNames($product);
            $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
            $mediaUrl =  $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            // Prepare data for CRM

            $parentProductName = 0;
            $parentId = NULL;
            if ($product->getTypeId() == 'simple') {
                $parentId = $this->getParentProductId($product->getId());
                $this->logger->info("Parent Product IDs: " . $product->getTypeId()."-". $parentId);
                if ($parentId) {
                    // Load the parent product
                    $parentProduct = $this->_productFactory->create()->load($parentId);

                    // Get the parent product name
                    $parentProductNameSku = $parentProduct->getSku();
                    $parnet_crm_ID = $this->getCrmIdBySku($parentProductNameSku);
                    $parentProductName = isset($parnet_crm_ID['crm_id']) ? (int) $parnet_crm_ID['crm_id'] : 0;

                    $this->logger->info("Parent Product IDs: " . $product->getTypeId() . "-" . $parentId."-". $parentProductName);
                    // Do something with the parent product name
                    // For example, log it or use it further
                }
            }
            $stockItem = $this->stockRegistry->getStockItem($product->getId());
            $quantity = $stockItem->getQty();
            //$parentProductName = 402000775749;
            // if ($product->getTypeId() == 'simple') {
            //     $configurableProduct = $product->getTypeInstance()->getParentIdsByChild($product->getId());
            //     $this->logger->info("Parent Product ID: " . $product->getTypeId());
            //     if (!empty($configurableProduct)) {
            //         $parentProductId = $configurableProduct[0];  // Get the first parent product ID
            //         $this->logger->info("Parent Product ID: " . $parentProductId);
                    
            //     }
            // }
            
          
            $crmId = $this->getCrmIdBySku($product->getSku());
            if(isset($crmId['crm_id']) && $crmId['crm_id'] != "" && $crmId['crm_id'] != NULL) {
             
                $crm_id = $crmId['crm_id'];
                $crm_update_response = (isset($crmId['crm_update_response']) && $crmId['crm_update_response'] != "") ? json_decode($crmId['crm_update_response'],true) : "";
                $priceID = "";
                $this->logger->info('CRM product_pricings Data: ' . print_r($crm_update_response['product']['product_pricings'][0], true));
                if(isset($crm_update_response['product']['product_pricings'][0]['id'])) {
                    $priceID = $crm_update_response['product']['product_pricings'][0]['id'];
                
                }
                $shippingTimeValue =  $product->getData('shipping_time');
                $status = $product->getStatus();

                $status =  ($status == 1) ?  true : false;
                $this->logger->info('CRM Data Status: ' .$status);
 
                $data = [
                    'product' => [
                        'name' => $product->getName(),
                        'description' => $product->getDescription(),
                        'category' => $categoriesString,
                        'is_active' => $status,
                        'parent_product' => $parentProductName,
                        'product_code' => $product->getTypeId(),
                        'sku_number' => $product->getSku(),
                        'product_pricings' => [
                            ["id"=> $priceID, "currency_code" => $currencyCode, "unit_price" => $product->getFinalPrice()]
                        ],
                        "custom_field" => [
                            "cf_mg_color" => "Yellow",
                            "cf_magento_catalog" => $categoriesString,
                            "cf_qnty"=> $quantity,
                            "cf_shipping_time" => $shippingTimeValue
                        ]
                    ],
                ];
                // Log CRM data
                $this->logger->info('CRM Data: ' . json_encode($data));

                $updateCrmResponse = $this->sendProductUpdateToCrm($crm_id, $data);
                $shippingTimeValue =  $product->getData('shipping_time');
                $updateData = [
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'category' => $categoriesString,
                    'is_active' => 1,
                    'parent_product' => $parentProductName,
                    'product_code' => $product->getTypeId(),
                    'sku_number' => $product->getSku(),
                    'product_pricings' => json_encode([
                        ["id" => $priceID, "currency_code" => $currencyCode, "unit_price" => $product->getFinalPrice()]
                    ]),
                    "currency_code" => $currencyCode,
                    'valid_till' => gmdate('Y-m-d H:i:s'),
                    'custom_field' => json_encode([
                        "cf_mg_color" => "Yellow",
                        "cf_magento_catalog" => $categoriesString,
                            "cf_qnty"=> $quantity
                    ]),
                    'crm_id' => $crm_id,
                    'update_date' => date('Y-m-d H:i:s'),
                    'crm_update_response' => $updateCrmResponse,
                    "shipping_time" => $shippingTimeValue
                ];
                $this->logger->info('CRM Table updateData Data: ' . json_encode($updateData));
                $this->updateProductInCrmTable($product->getSku(), $updateData);

            } else {
                $shippingTimeValue =  $product->getData('shipping_time');
                $status = $product->getStatus();
                $status =  ($status == 1) ?  true : false;
                $this->logger->info('CRM Data Status: ' . $status);
                $data = [
                    'product' => [
                        'name' => $product->getName(),
                        'description' => $product->getDescription(),
                        'category' => $categoriesString,
                        'is_active' =>  $status,
                        'parent_product' => $parentProductName,
                        'product_code' => $product->getTypeId(),
                        'sku_number' => $product->getSku(),
                        'product_pricings' => [
                            ["currency_code" => $currencyCode, "unit_price" => $product->getFinalPrice()]
                        ],
                        "custom_field" => [
                            "cf_mg_color" => "Yellow",
                            "cf_magento_catalog" => $categoriesString,
                            "cf_qnty"=> $quantity,
                            "cf_shipping_time" => $shippingTimeValue
                        ]
                    ],
                ];
                // Log CRM data
              

                // Send data to CRM
                $crmResponse = $this->sendToCrm($data);
                $crmResponseArray = json_decode($crmResponse, true);
                $crmId = $crmResponseArray['product']['id'] ?? null;

                $mediaGalleryEntries = $product->getMediaGalleryEntries();
                if (!empty($mediaGalleryEntries)) {
                    foreach ($mediaGalleryEntries as $entry) {
                        $mediaurls =  $mediaUrl . 'catalog/product' . $entry->getFile(); // This gives the file path of each image
                        $this->logger->info("CRM IMAGE:".$mediaurls);
                        $imageLabel = $entry->getLabel();
                        // echo $crm_id;
                        $this->sendImage($mediaurls, $crmId, $imageLabel);
                       // echo '<br>';
                    }
                } else {
                    $this->logger->info('No images found for this product.');
                    //echo 'No images found for this product.';
                }
            

                // Prepare data for local database insertion
                $insertData = [
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'category' => $categoriesString,
                    'is_active' => 1,
                    'parent_product' => $parentProductName,
                    'product_code' => $product->getTypeId(),
                    'sku_number' => $product->getSku(),
                    'product_pricings' => json_encode([
                        ["currency_code" => $currencyCode, "unit_price" => $product->getFinalPrice()]
                    ]),
                    "currency_code" => $currencyCode,
                    'valid_till' => gmdate('Y-m-d H:i:s'),
                    'custom_field' => json_encode([
                        "cf_mg_color" => "Yellow",
                        "cf_magento_catalog" => $categoriesString,
                            "cf_qnty"=> $quantity
                    ]),
                    'crm_id' => $crmId,
                    'create_date' => date('Y-m-d H:i:s'),
                    'update_date' => date('Y-m-d H:i:s'),
                    'crm_update_response' => $crmResponse,
                    "shipping_time" => $shippingTimeValue,
                    "crm_sec_update_response" => NULL
                ];
                $this->logger->info('CRM Data: ' . print_r($data, true));
               
                // Insert data into the database
                $connection = $this->resource->getConnection();
                $tableName = $connection->getTableName('crm_products');
                $connection->insert($tableName, $insertData);

                $this->logger->info('Data inserted successfully with ID: ' . $connection->lastInsertId());
            }
            } catch (\Exception $e) {
                $this->logger->error('Error sending product to CRM: ' . $e->getMessage());
            }
         
    }

    private function getCategoryNames(ProductInterface $product)
    {
        $categoryIds = $product->getCategoryIds();
        $categoryNames = [];

        if (!empty($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                try {
                    $category = $this->categoryRepository->get($categoryId);
                    $categoryNames[] = $category->getName();
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->logger->error('Error fetching category: ' . $e->getMessage());
                }
            }
        }

        return implode(', ', $categoryNames);
    }

    private function sendToCrm(array $data)
    {
        $crmUrl = 'https://thehouseofthings-org.myfreshworks.com/crm/sales/api/cpq/products'; // Replace with CRM API endpoint
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Token token=t6cbmvG6-1jolqKsJNb4eg', // Replace with your CRM API key
        ];
        $response = "";

        try {
            $this->curl->setHeaders($headers);
            $this->curl->post($crmUrl, json_encode($data));

            if ($this->curl->getStatus() !== 200) {
                throw new \Exception('CRM API call failed: ' . $this->curl->getBody());
            }

            $response = $this->curl->getBody();
            $this->logger->info('Product sent to CRM successfully: ' . $response);
        } catch (\Exception $e) {
            $this->logger->error('Error in CRM API call: ' . $e->getMessage());
        }
      //  $response = "";
        return $response;
    }

    private function sendImage($url, $pid, $imageLabel)
    {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://thehouseofthings-org.myfreshworks.com/crm/sales/api/document_links',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"url":"' . $url . '", "is_shared":false, "targetable_id":' . $pid . ', "targetable_type":"Product", "name":"' . $imageLabel . '" }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Token token=t6cbmvG6-1jolqKsJNb4eg',
                'content-type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function getCrmIdBySku($sku)
    {
        try {
            $connection = $this->resource->getConnection();
            $tableName = $connection->getTableName('crm_products');

            $query = "SELECT crm_id,crm_update_response FROM " . $tableName . " WHERE sku_number = :sku";
            $bind = ['sku' => $sku];

            $crmId = $connection->fetchRow($query, $bind);

            if ($crmId) {
                $this->logger->info("CRM ID found for SKU: " . $sku . " is " . print_r($crmId,true));
            } else {
                $this->logger->error("No CRM ID found for SKU: " . $sku);
            }

            return $crmId;
        } catch (\Exception $e) {
            $this->logger->error("Error fetching CRM ID for SKU: " . $sku . " - " . $e->getMessage());
            return null;
        }
    }

    public function sendProductUpdateToCrm($productId, $data)
    {
        //$crmUrl = 'https://thehouseofthings-org.myfreshworks.com/crm/sales/api/cpq/products/' . $productId . "?include=product_pricings";

        $crmUrl = "https://thehouseofthings-org.myfreshworks.com/crm/sales/api/cpq/products/$productId?include=product_pricings";
        $this->logger->info('Product update CRM URL: ' . $crmUrl);
        $this->logger->info('Product update CRM Data: ' ."'" . json_encode($data) . "'");
        $headers = [
            'Authorization: Token token=t6cbmvG6-1jolqKsJNb4eg',
            'Content-Type: application/json',
        ];

        try {
            // Initialize cURL
            $jsonPayload = json_encode($data);
            if (json_last_error() !== JSON_ERROR_NONE) {
              
                $this->logger->info('JSON Encoding Error: ' . json_last_error_msg());
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://thehouseofthings-org.myfreshworks.com/crm/sales/api/cpq/products/'.$productId.'?include=product_pricings',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Token token=t6cbmvG6-1jolqKsJNb4eg',
                    'Content-Type: application/json'
                ),
            ));


            // Execute cURL request
            $response = curl_exec($curl);



//curl_close($curl);
//echo $response;


            // Check for cURL errors
            if (curl_errno($curl)) {
                throw new \Exception('cURL Error: ' . curl_error($curl));
            }

            // Get HTTP status code
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            // Handle non-200 responses
            if ($httpStatus !== 200) {
                throw new \Exception('CRM API call failed with HTTP status: ' . $httpStatus . ' and response: ' . $response);
            }

            // Log successful response
            $this->logger->info('Product update sent to CRM successfully: ' . $response);

            return $response;
        } catch (\Exception $e) {
            // Log error and return message
            $this->logger->error('Error in sending product to CRM: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }

    public function addProductToCrmIfNotExists($productId, $data)
    {
        $crmBaseUrl = "https://thehouseofthings-org.myfreshworks.com/crm/sales/api/cpq/products";
        $headers = [
            'Authorization: Token token=t6cbmvG6-1jolqKsJNb4eg',
            'Content-Type: application/json',
        ];

        try {
            /** Step 1: Check if product exists **/
            $checkUrl = $crmBaseUrl . '/' . $productId;
            $this->logger->info('Checking product in CRM: ' . $checkUrl);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $checkUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers
            ]);
            curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpStatus === 200) {
                $this->logger->info("Product ID {$productId} already exists in CRM. Skipping add.");
                return "Product exists";
            }

            /** Step 2: Add product to CRM **/
            $this->logger->info("Product not found in CRM. Adding new product.");

            $jsonPayload = json_encode($data);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('JSON Encoding Error: ' . json_last_error_msg());
                throw new \Exception('Invalid product data JSON encoding');
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $crmBaseUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => $headers
            ]);
            $response = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpStatus !== 201 && $httpStatus !== 200) {
                throw new \Exception('CRM API create failed with HTTP status: ' . $httpStatus . ' and response: ' . $response);
            }

            $this->logger->info("Product successfully added to CRM: " . $response);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('Error adding product to CRM: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage();
        }
    }


    public function updateProductInCrmTable($sku, $data)
    {
        try {
            // Get the connection to the database
            $connection = $this->resource->getConnection();
            $tableName = $connection->getTableName('crm_products');  // Use your table name

            // Prepare the data to update
            $updateData = [
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'category' => $data['category'] ?? null,
                'is_active' => $data['is_active'] ?? null,
                'parent_product' => $data['parent_product'] ?? null,
                'product_code' => $data['product_code'] ?? null,
                'sku_number' => $data['sku_number'] ?? null,
                'product_pricings' => $data['product_pricings'] ?? null,
                'valid_till' => $data['valid_till'] ?? null,
                'custom_field' => $data['custom_field'] ?? null,
                'crm_id' => $data['crm_id'] ?? null,
            //    'crm_update_response' => $data['crm_update_response'] ?? null,
                'update_date' => date('Y-m-d H:i:s'), // Always update the date
                "shipping_time" => $data['shipping_time'] ?? null,
                "crm_sec_update_response"=>$data['crm_update_response'] ?? null
            ];

            // Prepare the condition to find the correct row (based on SKU)
            $condition = ['sku_number = ?' => $sku];

            // Perform the update query
            $connection->update($tableName, $updateData, $condition);

            $this->logger->info('Product updated successfully in CRM table.');

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Error updating product in CRM table: ' . $e->getMessage());
            return false;
        }
    }
    public function getParentProductName($childProductId)
    {
        try {
            // Get the child (simple) product object
           
            $childProduct = $this->productRepository->getById($childProductId);
            $this->logger->info('Gaurav Product updated successfully in CRM table.'. $childProductId);
         
            // Check if the product is a simple product
            // if ($childProduct->getTypeId() == 'simple') {
            //     // Get the parent (configurable) product
            //     $parentProduct = $childProduct->getParentProduct();
            //     $this->logger->error('product Name in CRM table: ' . $parentProduct);
            //     if ($parentProduct) {
            //         $this->logger->error('product Name in CRM table: ' . $parentProduct->getName());
           
            //         return $childProduct->getName(); // Return the parent product's name
            //     }
            // }
            return null;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // Handle exception (product not found)
            return null;
        }
    }

    private function getParentProductId($childProductId)
    {
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('catalog_product_super_link'); // Table linking simple to configurable products

        $select = $connection->select()
            ->from($tableName, 'parent_id')
            ->where('product_id = ?', $childProductId);

        $parentId = $connection->fetchOne($select);

        return $parentId;
    }
}
