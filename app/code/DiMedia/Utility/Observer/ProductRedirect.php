<?php
namespace DiMedia\Utility\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\UrlInterface;

class ProductRedirect implements ObserverInterface
{
    protected $productRepository;
    protected $request;
    protected $response;
    protected $categoryRepository;
    protected $redirect;
    protected $actionFlag;
    protected $url;

    public function __construct(
        ProductRepository $productRepository,
        RequestInterface $request,
        ResponseInterface $response,
        RedirectInterface $redirect,
        ActionFlag $actionFlag,
        CategoryRepository $categoryRepository,
        UrlInterface $url
    ) {
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->response = $response;
        $this->redirect = $redirect;
        $this->actionFlag = $actionFlag;
        $this->categoryRepository = $categoryRepository;
        $this->url = $url;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Get product ID from the request
        $productId = (int) $this->request->getParam('id');
   
        if (!$productId) {
            return;
        }

        try {
            // Load product by ID
            $product = $this->productRepository->getById($productId);

            if (!$product->getId() || $product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED) {
                // Get category IDs
                $categoryIds = $product->getCategoryIds();

                if (!empty($categoryIds)) {
                    // Get the last category assigned to the product
                    $lastCategoryId = end($categoryIds);
                    $category = $this->categoryRepository->get($lastCategoryId);
                   // $categoryUrl = $this->url->getUrl($category->getUrlPath());
                    $categoryUrl = $this->url->getUrl($category->getUrlPath() . '.html');

                    // Redirect to category URL
                    $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    $this->response->setRedirect($categoryUrl);
                }
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
