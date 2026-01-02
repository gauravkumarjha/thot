<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\OrderEraser\Controller\Adminhtml;

abstract class MassOrders extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_OrderEraser::delete';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|null
     */
    public $collectionFactory = null;

    /**
     * @var \Wyomind\OrderEraser\Model\ResourceModel\Orders\CollectionFactory|null
     */
    public $oeCollectionFactory = null;

    /**
     * MassOrders constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Wyomind\OrderEraser\Model\ResourceModel\Orders\CollectionFactory $oeCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Wyomind\OrderEraser\Model\ResourceModel\Orders\CollectionFactory $oeCollectionFactory
    ) {
    
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->oeCollectionFactory = $oeCollectionFactory;
    }
}
