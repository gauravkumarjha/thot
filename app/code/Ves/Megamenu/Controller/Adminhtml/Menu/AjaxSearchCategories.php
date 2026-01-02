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

class AjaxSearchCategories extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Ves\Megamenu\Model\Config\Source\StoreCategories
     */
    protected $storeCategories;

    /**
     * @var \Ves\Megamenu\Helper\Editor
     */
    protected $editor;

    /**
     * AjaxSearchCategories constructor.
     *
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Framework\App\ResourceConnection         $resource
     * @param \Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories
     * @param \Ves\Megamenu\Helper\Editor                       $_editor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories,
        \Ves\Megamenu\Helper\Editor $_editor
    ) {
        parent::__construct($context);
        $this->_resource       = $resource;
        $this->storeCategories = $storeCategories;
        $this->editor          = $_editor;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $keyword           = $this->getRequest()->getParam('keyword');
        $data              = [];
        $data['tree_html'] = $this->editor->getSearchCategoriesOptionsHtml($keyword);

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($data)
        );

        return;
    }

    /**
     * @return array
     */
    public function getStoreCategories()
    {
        return $this->storeCategories->getCategoriesTree();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_Megamenu::menu_edit');
    }
}
