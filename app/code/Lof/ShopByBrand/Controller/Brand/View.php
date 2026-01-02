<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ShopByBrand
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\ShopByBrand\Controller\Brand;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class View extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Ves\Brand\Model\Brand
     */
    protected $_brandModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Ves\Brand\Helper\Data
     */
    protected $_brandHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    protected $_attributeCode;

    /**
     * Catalog design
     *
     * @var \Magento\Catalog\Model\Design
     */
    protected $_catalogDesign;

    protected $_brand;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    protected $categoryUrlPathGenerator;

    /**
     * @param Context                                             $context              [description]
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager         [description]
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    [description]
     * @param \Ves\Brand\Model\Brand                              $brandModel           [description]
     * @param \Magento\Framework\Registry                         $coreRegistry         [description]
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory [description]
     * @param \Ves\Brand\Helper\Data                              $brandHelper          [description]
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ves\Brand\Model\Brand $brandModel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Ves\Brand\Helper\Data $brandHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_brandModel = $brandModel;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_brandHelper = $brandHelper;
        $this->_storeManager = $storeManager;
        $this->layerResolver = $layerResolver;
        $this->categoryRepository = $categoryRepository;
        $this->_catalogDesign = $catalogDesign;
        $this->_attributeCode = \Ves\Brand\Model\Brand::ATTRIBUTE_CODE;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    public function _initBrand()
    {
        $brandId = (int)$this->getRequest()->getParam('brand_id', false);
        if (!$brandId) {
            return false;
        }
        try {
            $brand = $this->_brandModel->load($brandId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_brand', $brand);
        $this->_brand = $brand;
        return $brand;
    }

     /**
      * Initialize requested category object
      *
      * @return \Magento\Catalog\Model\Category
      */

    protected function _initCategory()
    {
        $brandId = (int)$this->getRequest()->getParam('brand_id', false);
        $categoryId = $this->_storeManager->getStore()->getRootCategoryId();//(int)$this->getRequest()->getParam('id', $this->_storeManager->getStore()->getRootCategoryId());
        if (!$brandId) {
            return false;
        }
        $brand = $this->_initBrand();
        try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
        // if (!$this->_objectManager->get('Magento\Catalog\Helper\Category')->canShow($category)) {
            // return false;
        // }
        //$this->_catalogSession->setLastVisitedCategoryId($category->getId());

        /* get all products of children categories */
        $category->setIsAnchor(true);

        $this->_coreRegistry->register('current_category', $category);
        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                ['category' => $category, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return false;
        }

        return $category;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->_brandHelper->getConfig('general_settings/enable')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $category = $this->_initCategory();
        if ($category) {
            if (!$this->layerResolver->get(Resolver::CATALOG_LAYER_CATEGORY)) {
                $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
            }

           // $this->layerResolver->get(Resolver::CATALOG_LAYER_CATEGORY)->getProductCollection()->addFieldToFilter(
           //     $this->_attributeCode, $brandId
           // );
            if ($this->_brand->getAttributeOptionId()) {
                $this->layerResolver->get(Resolver::CATALOG_LAYER_CATEGORY)
                                    ->getProductCollection()
                                    ->addFieldToFilter($this->_attributeCode, (int)$this->_brand->getAttributeOptionId());
            }
            $this->getRequest()->setParam('ves_disable_' . $this->_attributeCode, true);
            $settings = $this->_catalogDesign->getDesignSettings($category);

            $page = $this->resultPageFactory->create();
            // apply custom layout (page) template once the blocks are generated
            if ($settings->getPageLayout()) {
                $page->getConfig()->setPageLayout($settings->getPageLayout());
            }

            $hasChildren = $category->hasChildren();
            $type = $hasChildren ? 'layered' : 'default_without_children';

            if (!$hasChildren) {
                // Two levels removed from parent.  Need to add default page type.
                $parentType = strtok($type, '_');
                $page->addPageLayoutHandles(['type' => $parentType]);
            }
            $page->addPageLayoutHandles(['type' => $type, 'id' => $category->getId()]);
            $page->getConfig()->addBodyClass('page-products')
                ->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($category))
                ->addBodyClass('category-' . $category->getUrlKey())
                ->addBodyClass('catalog-category-view');
            return $page;
        } elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}
