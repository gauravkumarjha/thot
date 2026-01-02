<?php
namespace Magecomp\Gstcharge\Model\ResourceModel\Gstreportitemwise;

class Collection extends \Magento\Reports\Model\ResourceModel\Order\Collection
{
    protected $_idFieldName = "item_id";
    public function setDateRange($from, $to)
    {
        $this->setMainTable('sales_order_item');
        $this->_reset();
        $this->getSelect()
             ->joinLeft(
                 ['order' => $this->getTable('sales_order')],
                 'main_table.order_id = order.entity_id'
             )
            ->joinLeft(
                ['order_address' => $this->getTable('sales_order_address')],
                'main_table.order_id = order_address.entity_id'
            )
            ->where("main_table.created_at BETWEEN '".$from."' AND '".$to."'");

            $this->getSelect()->group('main_table.item_id');

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
