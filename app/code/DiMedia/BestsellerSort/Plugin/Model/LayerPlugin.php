<?php 
namespace DiMedia\BestsellerSort\Plugin\Model;

use Magento\Catalog\Model\Layer;
use Magento\Framework\App\RequestInterface;
use Zend_Db_Expr;

class LayerPlugin
{
    protected $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function afterGetProductCollection(Layer $subject, $collection)
    {
        $currentOrder = $this->request->getParam('product_list_order');
        $currentDir = $this->request->getParam('product_list_dir', 'desc');

        if ($currentOrder === 'bestseller') {
            $select = $collection->getSelect();
            $fromPart = $select->getPart(\Zend_Db_Select::FROM);

            if (!isset($fromPart['order_item_bs'])) {
                $select->joinLeft(
                    ['order_item_bs' => $collection->getTable('sales_order_item')],
                    'e.entity_id = order_item_bs.product_id',
                    []
                );
                $select->columns(['ordered_qty' => new \Zend_Db_Expr('IFNULL(SUM(order_item_bs.qty_ordered), 0)')]);

                $select->group('e.entity_id');
                $select->order('ordered_qty ' . strtoupper($currentDir));
            }
        }

        return $collection;
    }
}
