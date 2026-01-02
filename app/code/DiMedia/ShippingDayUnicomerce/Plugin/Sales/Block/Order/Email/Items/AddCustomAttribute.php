<?php

namespace DiMedia\ShippingDayUnicomerce\Plugin\Sales\Block\Order\Email\Items;

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
            $getCustomShippingPrice = $childitem->getFulfillmentDate();
        } else {
            $productId = $item->getProductId();
            $getCustomShippingPrice = $item->getFulfillmentDate();;
        }
        
        if ($getCustomShippingPrice) {
            $result[] = [
                'label' => __('Shipped by '),
                'value' => $getCustomShippingPrice,
            ];
        }

        $writer = new Zend_Log_Writer_Stream(BP . '/var/log/shippedDate.log');
        $logger = new Zend_Log();
        $logger->addWriter($writer);
        $logger->info(print_r($result, true));


        return $result;
    }
}
