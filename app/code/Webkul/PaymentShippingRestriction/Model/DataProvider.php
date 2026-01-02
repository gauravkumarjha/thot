<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PaymentShippingRestriction
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PaymentShippingRestriction\Model;
 
use Webkul\PaymentShippingRestriction\Model\ResourceModel\ShippingMapping\CollectionFactory;
 
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Ui\DataProvider\AbstractDataProvider
     */
    protected $_loadedData;
    
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $mappingCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $mappingCollectionFactory,
        \Webkul\PaymentShippingRestriction\Model\ShippingMappingFactory $shippingMapping,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $mappingCollectionFactory->create();
        $this->shippingMapping = $shippingMapping;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
 
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        $paymentMethods = [];
        foreach ($items as $mapping) {
            $shippingMethod = $mapping->getShippingCode();
            $dataCollection = $this->shippingMapping->create()->getCollection()
            ->addFieldToFilter('shipping_code', ['eq'=>$shippingMethod]);
            $paymentMethods = $this->getPaymentMethodsArray($dataCollection);
            $mapping->setPaymentCode($paymentMethods);
            $this->_loadedData[$mapping->getEntityId()] = $mapping->getData();
        }
        return $this->_loadedData;
    }
    
    /**
     * payment method array
     *
     * @param array $dataCollection
     * @return array
     */
    private function getPaymentMethodsArray($dataCollection)
    {
        $paymentMethods = [];
        try {
            if (!empty($dataCollection)) {
                foreach ($dataCollection as $data) {
                    array_push($paymentMethods, $data->getPaymentCode());
                }
            }
            return $paymentMethods;
        } catch (\Exception $e) {
            return $paymentMethods;
        }
    }
}
