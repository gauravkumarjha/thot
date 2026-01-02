<?php

namespace Mageplaza\Simpleshipping\Plugin\Sales\Block\Adminhtml\Order\View\Items\Renderer;

use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as ItemCollectionFactory;

class DefaultRendererPlugin
{

    protected $itemCollectionFactory;

    public function __construct(
        ItemCollectionFactory $itemCollectionFactory
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
    }

    /**
     * Add custom attribute to the item's rendered output
     *
     * @param DefaultRenderer $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(DefaultRenderer $subject, $result)
    {
        // $writer = new Zend_Log_Writer_Stream(BP . '/var/log/backend.log');
        // $logger = new Zend_Log();
        // $logger->addWriter($writer);
        // $logger->info("Observer started");

        $item = $subject->getItem();
        $itemid = $item->getItemId();

        $childitem = $this->itemCollectionFactory->create()
        ->addFieldToFilter('parent_item_id', $itemid)
        ->getFirstItem();
        if($childitem->getProductId()) {
            $productId = $childitem->getProductId();
            $getCustomShippingPrice = $childitem->getCustomShippingPrice();
            
        } else {
            $productId = $item->getProductId();
            $getCustomShippingPrice = $item->getCustomShippingPrice();;
        }

        if (
            is_int($getCustomShippingPrice) || (is_numeric($getCustomShippingPrice) && (int)$getCustomShippingPrice == $getCustomShippingPrice)) {
           
            $getCustomShippingPrice = "₹" . $getCustomShippingPrice . ".00";
        } else{
            $getCustomShippingPrice = $getCustomShippingPrice;
        }
        // Append custom attribute to the result
         $customHtml="";
        if($getCustomShippingPrice) {
            $customHtml = '<tr><td><strong>shipping:</strong></td><td>' . $getCustomShippingPrice . '</td></tr>';
        }
      
        return $result . $customHtml;
    }
}
