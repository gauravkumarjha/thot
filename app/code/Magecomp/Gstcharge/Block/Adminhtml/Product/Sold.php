<?php

namespace Magecomp\Gstcharge\Block\Adminhtml\Product;

class Sold extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected $_blockGroup = 'Magecomp_Gstcharge';

    protected function _construct()
    {
        $this->_blockGroup = 'Magecomp_Gstcharge';
        $this->_controller = 'adminhtml_gstreport';
        $this->_headerText = __('Gst Report');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
