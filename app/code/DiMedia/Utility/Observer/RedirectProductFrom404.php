<?php
namespace DiMedia\Utility\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;

class RedirectProductFrom404 implements ObserverInterface
{
    protected $request;
    protected $url;
    protected $response;
    protected $actionFlag;
    protected $storeManager;
    protected $redirect;
    protected $productCollectionFactory;

    public function __construct(
        RequestInterface $request,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        UrlInterface $url,
        ResponseInterface $response,
        ActionFlag $actionFlag,
        StoreManagerInterface $storeManager,
        RedirectInterface $redirect
    ) {
        $this->request = $request;
        $this->url = $url;
        $this->response = $response;
        $this->actionFlag = $actionFlag;
        $this->storeManager = $storeManager;
        $this->redirect = $redirect;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function execute(Observer $observer)
    {
        $requestPath = trim($this->request->getPathInfo(), '/'); // e.g., lighting/table-lamps/beam-i-table-lamp.html
        $urlKey = basename($requestPath, '.html'); // beam-i-table-lamp
     
        try {
            // $product = $this->productRepository->get($urlKey);
            $productCollection = $this->productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('url_key', $urlKey)
                ->addAttributeToFilter('visibility', ['neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE])
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->setPageSize(1)
                ->load();
            $product = $productCollection->getFirstItem();
            if ($product->getId() && $product->getStatus() == Status::STATUS_ENABLED) {
                $productUrl = $product->getProductUrl();

                $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
                $this->response->setRedirect($productUrl);
            }
        } catch (\Exception $e) {
          
            // Product not found, do nothing.
        }
    }
}
