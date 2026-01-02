<?php


namespace Magecomp\Gstcharge\Controller\Adminhtml\Order\Creditmemo;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Backend\App\Action;

class NewAction extends \Magento\Backend\App\Action implements HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Magento_Sales::sales_creditmemo';


    protected $creditmemoLoader;


    protected $resultPageFactory;

    protected $resultForwardFactory;
    protected $order;


    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\Order $order,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->order = $order;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }


    public function execute()
    {

                $order = $this->order->load($this->getRequest()->getParam('order_id'));

                $charge = 0;
        foreach ($order->getAllItems() as $item) {
            $charge += $item->getQtyToRefund() * $item->getPerProduct();
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

        $this->creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
        $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
        $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
        $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
        $creditmemo = $this->creditmemoLoader->load();
        if ($creditmemo) {
            if ($comment = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true)) {
                $creditmemo->setCommentText($comment);
            }
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Magento_Sales::sales_order');
            $resultPage->getConfig()->getTitle()->prepend(__('Credit Memos'));
            if ($creditmemo->getInvoice()) {
                $resultPage->getConfig()->getTitle()->prepend(
                    __("New Memo for #%1", $creditmemo->getInvoice()->getIncrementId())
                );
            } else {
                $resultPage->getConfig()->getTitle()->prepend(__("New Memo"));
            }
            return $resultPage;
        } else {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }
}
