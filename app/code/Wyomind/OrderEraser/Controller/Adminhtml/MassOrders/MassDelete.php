<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\OrderEraser\Controller\Adminhtml\MassOrders;

class MassDelete extends \Wyomind\OrderEraser\Controller\Adminhtml\MassOrders
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_OrderEraser::delete';

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return mixed
     */
    protected function massAction(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection)
    {
        $path = "sales/order/index";
        
        $params = $this->getRequest()->getParams();

        $ids = [];

        if (isset($params['excluded']) && $params['excluded'] == "false") {
            $ids[] = '*';
        } else {
            if (isset($params['selected'])) {
                if (is_array($params['selected'])) {
                    $ids = $params['selected'];
                } else {
                    $ids = [$params['selected']];
                }
            }
        }

        $countDeleteOrder = 0;

        if (count($ids) > 0) {
            $oeCollection = $this->oeCollectionFactory->create();
            if ($collection->getSize()) {
                foreach ($collection->getItems() as $order) {
                    $oeCollection->deleteOrder($order->getId());
                    $countDeleteOrder++;
                }
            } else {
                foreach ($ids as $id) {
                    $oeCollection->deleteOrder($id);
                    $countDeleteOrder++;
                }
            }

            $this->messageManager->addSuccess($countDeleteOrder . __(' order(s) successfully deleted'));
        } else {
            $this->messageManager->addError(__('Unable to delete orders.'));
        }
        
        return $this->resultRedirectFactory->create()->setPath($path, []);
    }
}
