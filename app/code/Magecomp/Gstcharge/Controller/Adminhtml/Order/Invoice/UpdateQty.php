<?php
namespace Magecomp\Gstcharge\Controller\Adminhtml\Order\Invoice;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice\View as AbstractView;

class UpdateQty extends AbstractView implements HttpPostActionInterface
{

    protected $resultJsonFactory;


    protected $resultPageFactory;

    protected $resultRawFactory;

    private $invoiceService;


    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        RawFactory $resultRawFactory,
        InvoiceService $invoiceService
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->invoiceService = $invoiceService;
        parent::__construct($context, $registry, $resultForwardFactory);
    }


    public function execute()
    {
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $invoiceData = $this->getRequest()->getParam('invoice', []);
            $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];
    
            $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }

            $charge = 0 ;
            foreach ($invoiceItems as $invoiceitem => $qty) {
                foreach ($order->getAllItems() as $item) {
                    if ($invoiceitem == $item->getId()) {
                        $charge += $qty * $item->getPerProduct();
                    }
                }
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


            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }

            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);

            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The invoice can't be created without products. Add products and try again.")
                );
            }
            $this->registry->register('current_invoice', $invoice);
            $invoiceRawCommentText = $invoiceData['comment_text'];
            $invoice->setCommentText($invoiceRawCommentText);

            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('Invoices'));
            $response = $resultPage->getLayout()->getBlock('order_items')->toHtml();
        } catch (LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Cannot update item quantity.')];
        }
        if (is_array($response)) {
           
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        } else {
            
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setContents($response);
            return $resultRaw;
        }
    }
}
