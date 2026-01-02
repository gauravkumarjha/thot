<?php

namespace Magecomp\Gstcharge\Controller\Adminhtml\Order\Creditmemo;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Backend\App\Action;

class UpdateQty extends \Magento\Backend\App\Action implements HttpPostActionInterface
{

    const ADMIN_RESOURCE = 'Magento_Sales::sales_creditmemo';


    protected $creditmemoLoader;


    protected $resultPageFactory;

    protected $resultJsonFactory;

    protected $resultRawFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
             
            $orderId = $this->getRequest()->getParam('order_id');
            
            $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
          
            
            $creditmemoItem = $this->getRequest()->getParam('creditmemo');
            $charge = 0 ;
            foreach ($creditmemoItem['items'] as $key => $val) {
                foreach ($order->getAllItems() as $item) {

                    
                    if ($key == $item->getId()) {
                        $charge += $val['qty'] * $item->getPerProduct();
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
           
            $this->creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
            $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
            $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
            $this->creditmemoLoader->load();



            $resultPage = $this->resultPageFactory->create();
            $response = $resultPage->getLayout()->getBlock('order_items')->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('We can\'t update the item\'s quantity right now.')];
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
