<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Grid\Renderer;

use Magento\Catalog\Model\ProductRepository;
use Magento\Sales\Model\Order;

class Hsncode extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $orderObj;
    protected $product;

    public function __construct(
        Order $order,
        ProductRepository $product
    ) {
        $this->orderObj = $order;
        $this->product = $product;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $value = parent::render($row);
        $hsncode = "";
        if ($value > 0) {
            $order = $this->orderObj->load($value);
            $orderItems = $order->getAllVisibleItems();
            $countOrderItem = count($orderItems);
            $i = 0;

            foreach ($orderItems as $key => $item) {
                $i++;
                $products = $this->product->getById($item->getProductId());
                if ($products->getHsncode()) {
                    $hsncode .= $products->getHsncode() . ($countOrderItem == $i ? "" : ",");
                }

            }
        }
        return $hsncode;
    }
}
