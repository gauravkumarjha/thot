<?php
namespace Magecomp\Gstcharge\Controller\Adminhtml\Gstreportitemwise;

class Index extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    public function execute()
    {

        $this->_initAction()->_setActiveMenu(
            'Magecomp_Gstcharge::gstreportitemwise'
        )->_addBreadcrumb(
            __('Gst Report Item Wise'),
            __('Gst Report Item Wise')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Gst Report Item Wise'));
        $this->_view->renderLayout();
    }
}
