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

namespace Lof\ShopByBrand\Controller\Adminhtml\Items;

class NewAction extends \Lof\ShopByBrand\Controller\Adminhtml\Items
{
    protected $_helperAttribute;

     /**
      * Initialize Group Controller
      *
      * @param \Magento\Backend\App\Action\Context $context
      * @param \Magento\Framework\Registry $coreRegistry
      * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
      * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
      * @param \Lof\ShopByBrand\Helper\Attribute $helperAttribute
      */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\ShopByBrand\Helper\Attribute $helperAttribute
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory);
        $this->_helperAttribute = $helperAttribute;
    }

    public function execute()
    {
        $this->_helperAttribute->processSyncBrandAttribute();
        $this->messageManager->addSuccess(__('All Brands Re-Synced'));
        $this->_redirect('lof_shopbybrand/*/');
    }
}
