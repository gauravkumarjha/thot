<?php

namespace Mageplaza\Simpleshipping\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;

class ValidateProductAttribute
{
    public function beforeSave(Product $subject)
    {
        // Replace 'custom_attribute' with your attribute code
        $attributeValue = $subject->getData('shipping_charges_feature_enabl');

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/shipping_method-3.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

   $logger->info($attributeValue); //to log the array

        if($attributeValue  == 1 ) {
            $weight_price = $subject->getData('weight_price');
            $logger->info("weight_price-".$weight_price); //to log the array
            if($weight_price == "" || $weight_price == null ) {
                throw new LocalizedException(__('Please enter your weight price value.'));
            } else if (!$this->isValid($weight_price)) {
                throw new LocalizedException(__('Please enter your weight price value.'));
            }
        }
        return [$subject];
    }

    /**
     * Validate the custom attribute value
     *
     * @param string $attributeValue
     * @return bool
     */
    protected function isValid($attributeValue)
    {
        // Add your validation logic here
        return preg_match('/^[a-zA-Z0-9]+$/', $attributeValue);
    }
}
