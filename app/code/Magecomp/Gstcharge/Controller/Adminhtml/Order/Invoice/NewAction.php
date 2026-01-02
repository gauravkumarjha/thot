<?php

namespace Magecomp\Gstcharge\Controller\Adminhtml\Order\Invoice;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;

class NewAction extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Magento_Sales::sales_invoice';

  
    protected $registry;

    protected $resultPageFactory;

  
    private $invoiceService;

    private $orderRepository;

   
    public function __construct(
        Action\Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        InvoiceService $invoiceService,
        OrderRepositoryInterface $orderRepository = null
    ) {
        parent::__construct($context);

        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->invoiceService = $invoiceService;
        $this->orderRepository = $orderRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(OrderRepositoryInterface::class);
    }

    
    protected function _redirectToOrder($orderId)
    {
       
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        return $resultRedirect;
    }

   
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $invoiceData = $this->getRequest()->getParam('invoice', []);
        $invoiceItems = $invoiceData['items'] ?? [];

        try {
            $order = $this->orderRepository->get($orderId);

            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }

                $order = $this->orderRepository->get($orderId);
                $charge = 0;
            foreach ($order->getAllItems() as $item) {
                $charge += $item->getQtyToInvoice() * $item->getPerProduct();
            }
            if ($charge != 0) {
                if ($order->getCgstCharge()>0) {
                    $order->setCgstCharge($charge/2);
                }
                if ($order->getSgstCharge()>0) {
                    $order->setSgstCharge($charge/2);
                }
                if ($order->getIgstCharge()>0) {
                    $order->setIgstCharge($charge);
                }
                $order->save();
                   
            }



            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);

            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The invoice can't be created without products. Add products and try again.")
                );
            }
            $this->registry->register('current_invoice', $invoice);

            $comment = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true);
            if ($comment) {
                $invoice->setCommentText($comment);
            }

            
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Magento_Sales::sales_order');
            $resultPage->getConfig()->getTitle()->prepend(__('Invoices'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Invoice'));
            return $resultPage;
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $this->_redirectToOrder($orderId);
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, 'Cannot create an invoice.');
            return $this->_redirectToOrder($orderId);
        }
    }
}
