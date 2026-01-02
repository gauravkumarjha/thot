<?php
namespace Mageplaza\Simpleshipping\Plugin\Sales\Block\Order\Email\Items;

use Magento\Sales\Block\Order\Email\Items\Order as EmailOrderItems;

use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as ItemCollectionFactory;

class AddCustomAttribute
{
    protected $itemCollectionFactory;

    public function __construct(
        ItemCollectionFactory $itemCollectionFactory
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
    }
    /**
     * After getItemOptions plugin
     *
     * @param EmailOrderItems $subject
     * @param array $result
     * @return array
     */

    public function afterGetItemOptions(EmailOrderItems $subject, $result)
    {
        $item = $subject->getItem();
        $itemid = $item->getItemId();

        
        $childitem = $this->itemCollectionFactory->create()
        ->addFieldToFilter('parent_item_id', $itemid)
        ->getFirstItem();
        if ($childitem->getProductId()) {
            $productId = $childitem->getProductId();
            $getCustomShippingPrice = $childitem->getCustomShippingPrice();
        } else {
            $productId = $item->getProductId();
            $getCustomShippingPrice = $item->getCustomShippingPrice();;
        }
        if (
            is_int($getCustomShippingPrice) || (is_numeric($getCustomShippingPrice) && (int)$getCustomShippingPrice == $getCustomShippingPrice)
        ) {

            $getCustomShippingPrice = "₹" . $getCustomShippingPrice . ".00";
        } else {
            $getCustomShippingPrice = $getCustomShippingPrice;
        }
        //$customAttribute = $item->getProduct()->getData('custom_attribute_code'); // Replace with your actual attribute code

        if ($getCustomShippingPrice) {
            $result[] = [
                'label' => __('Shipping'),
                'value' => $getCustomShippingPrice,
            ];
        }

        $writer = new Zend_Log_Writer_Stream(BP . '/var/log/emailfire.log');
        $logger = new Zend_Log();
        $logger->addWriter($writer);
        $logger->info(print_r($result,true));


        return $result;
    }
}
