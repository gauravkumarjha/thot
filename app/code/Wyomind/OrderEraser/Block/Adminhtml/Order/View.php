<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\OrderEraser\Block\Adminhtml\Order;

/**
 * Render the export button in order > view
 */
class View
{
    public function __construct(\Wyomind\OrderEraser\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * Interceptor for getOrder
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     */
    public function beforeGetOrder(\Magento\Sales\Block\Adminhtml\Order\View $subject)
    {
        if ($this->authorization->isAllowed('Wyomind_OrderEraser::delete')) {
            $subject->addButton('void_delete', ['label' => __('Delete'), 'onclick' => 'setLocation(\'' . $this->urlInterface->getUrl('ordereraser/orders/delete', ['selected' => $subject->getRequest()->getParam('order_id')]) . '\')']);
        }
    }
}