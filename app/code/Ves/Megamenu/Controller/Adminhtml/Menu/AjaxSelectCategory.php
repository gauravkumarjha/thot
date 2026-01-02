<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2017 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Megamenu\Controller\Adminhtml\Menu;

class AjaxSelectCategory extends \Magento\Backend\App\Action
{

    /**
     * @var \Ves\Megamenu\Model\Config\Source\StoreCategories
     */
    protected $storeCategories;

    /**
     * @var \Ves\Megamenu\Helper\Editor
     */
    protected $editor;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $categoryModel;

    /**
     * AjaxSearchCategories constructor.
     *
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories
     * @param \Ves\Megamenu\Helper\Editor                       $_editor
     * @param \Magento\Catalog\Model\Category $categoryModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories,
        \Ves\Megamenu\Helper\Editor $_editor,
        \Magento\Catalog\Model\Category $categoryModel
    ) {
        parent::__construct($context);
        $this->storeCategories = $storeCategories;
        $this->editor          = $_editor;
        $this->categoryModel   = $categoryModel;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $id                    = $this->getRequest()->getParam('id');
        $data                  = [];
        $data['selected_html'] = '';

        $category = $this->categoryModel->load($id);
        if ($category->getData()) {
            $data['selected_html'] = '(ID: ' . $category->getId() . ') ' . $category->getName();
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($data)
            );
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_Megamenu::menu_edit');
    }
}
