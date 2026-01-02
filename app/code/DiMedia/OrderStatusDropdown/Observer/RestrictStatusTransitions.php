<?php

namespace DiMedia\OrderStatusDropdown\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;

use Psr\Log\LoggerInterface;

class RestrictStatusTransitions implements ObserverInterface
{
    protected $logger;
    protected $orderRepository;
    protected $messageManager;
    protected $redirectFactory;

    public function __construct(
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        ManagerInterface $messageManager,
        RedirectFactory $redirectFactory
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $newStatus = $order->getStatus();

        $allowedTransitions = [
            'pending' => 'new',
            'processing' => 'processing',
            'canceled' => 'canceled',
            'shipped' => 'processing',
            'partially_shipped' => 'processing',
            'returned' => 'processing',
            'partially_returned' => 'processing',
            'partially_replaced' => 'processing',
            'partially_refunded' => 'processing',
            'partially_delivered' => 'processing',
            'delivered' => 'processing',
            'replaced' => 'processing',
            'refunded' => 'processing',
        ];
        
        $newState = $allowedTransitions[$newStatus] ?? 'new';
        $comment = 'Manually updated order status.';

        $this->logger->info("Change Order Status - Order ID: {$order->getId()} - New State: {$newState} - New Status: {$newStatus} - Comment: {$comment}");
        $order->setState($newState)
            ->setStatus($newStatus)
            ->addStatusHistoryComment($comment)
            ->setIsCustomerNotified(false);

        $this->orderRepository->save($order);
        try {
            $this->orderRepository->save($order);
            $this->messageManager->addSuccessMessage(__('Order status updated. Reloading page...'));
        } catch (\Exception $e) {
            $this->logger->error('Error updating order status: ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Failed to update order status.'));
        }
        return $this->redirectFactory->create()->setPath('*/*/');
        // try {
          
        // } catch (\Magento\Framework\Exception\LocalizedException $e) {
        //     $this->logger->error('Error updating order status: ' . $e->getMessage());
        //     throw $e;
        // }
    }
}
