<?php
namespace Magecomp\Gstcharge\Model\ResourceModel\Gstreport;

class Collection extends \Magento\Reports\Model\ResourceModel\Order\Collection
{
    public function setDateRange($from, $to)
    {
        $this->_reset()->addAttributeToSelect(
            '*'
        );

        $this->getSelect()
             ->join(
                 ['order_items' => $this->getTable('sales_order_item')],
                 'main_table.entity_id = order_items.order_id'
             )
             ->joinLeft(
                 ['order' => $this->getTable('sales_order')],
                 'order_items.order_id = order.entity_id'
             )
             ->joinLeft(
                 ['order_address' => $this->getTable('sales_order_address')],
                 'order.shipping_address_id = order_address.entity_id'
             )
            ->where("order_items.created_at BETWEEN '".$from."' AND '".$to."'");
            $this->getSelect()->group('main_table.entity_id');

        return $this;
    }

    public function setStoreIds($storeIds)
    {
        return $this;
    }

   
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, ['orders', 'ordered_qty'])) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }
}
