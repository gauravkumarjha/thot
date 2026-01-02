<?php
namespace Magecomp\Gstcharge\Controller\Adminhtml\Gstreport;

class Index extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    public function execute()
    {

        $this->_initAction()->_setActiveMenu(
            'Magecomp_Gstcharge::gstreport'
        )->_addBreadcrumb(
            __('Gst Report'),
            __('Gst Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Gst Report'));
        $this->_view->renderLayout();
    }
}
