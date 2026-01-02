<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\OrderEraser\Controller\Adminhtml;

abstract class Orders extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_OrderEraser::delete';

    /**
     * @var \Wyomind\OrderEraser\Model\ResourceModel\Orders\CollectionFactory|null
     */
    public $ordersCollectionFactory = null;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|null
     */
    public $searchCriteriaBuilder = null;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface|null
     */
    public $orderRepository = null;

    /**
     * Orders constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Wyomind\OrderEraser\Model\ResourceModel\Orders\CollectionFactory $ordersCollectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Wyomind\OrderEraser\Model\ResourceModel\Orders\CollectionFactory $ordersCollectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
    
        $this->ordersCollectionFactory = $ordersCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    abstract public function execute();
}
