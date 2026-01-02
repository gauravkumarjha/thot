<?php

namespace DiMedia\BestsellerSort\Block\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    protected $_collection = null;
   
    protected function _getAvailableOrders()
    {
        $orders = parent::_getAvailableOrders();
        $orders['bestseller'] = __('Best Sellers');
        return $orders;
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // Set pagination
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }

        // Handle sorting
        if ($this->getCurrentOrder()) {
            $order = $this->getCurrentOrder();
            $direction = $this->getCurrentDirection();

            if ($order == 'bestseller') {
                $this->addBestsellerSorting($this->_collection, $direction);
            
       // echo $collection->getSelect();
     
            } else {
                parent::setCollection($collection);
            }
        }

        return $this;
    }

    protected function addBestsellerSorting($collection, $direction = 'desc')
    {
        $select = $collection->getSelect();

        // Avoid duplicate join by checking if alias is already used
        $fromPart = $select->getPart(\Zend_Db_Select::FROM);
        if (!array_key_exists('order_itembcx', $fromPart)) {
            $select->joinLeft(
                ['order_itembcx' => $collection->getResource()->getTable('sales_order_item')],
                'e.entity_id = order_itembcx.product_id',
                ['ordered_qty' => new \Zend_Db_Expr('SUM(order_itembcx.qty_ordered)')]
            );

            $select->group('e.entity_id');
        }
        $direction = "DESC";    
        // Set order by ordered quantity
        $select->order("ordered_qty {$direction}");
    }
}
